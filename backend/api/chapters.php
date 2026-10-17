<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM chapters WHERE id = ?');
$stmt->execute([$id]);
$chapter = $stmt->fetch();
if (!$chapter) json_out(['error' => 'Not found'], 404);

$s = $pdo->prepare('SELECT * FROM series WHERE id = ?');
$s->execute([$chapter['series_id']]);
$series = $s->fetch();

// ✅ CHANGED: gate applies to ANY locked chapter (novels AND imported manhwa/manhua)
// 🔒 Lock only if chapter is paid AND the series actually charges money
$price = (float)($series['novel_price'] ?? 0);
if (!$chapter['is_free'] && $price > 0) {
    $user = current_user();
    $owned = false;
    if ($user) {
        $p = $pdo->prepare("SELECT 1 FROM purchases WHERE user_id = ? AND series_id = ? AND status = 'paid'");
        $p->execute([$user['id'], $series['id']]);
        $owned = (bool)$p->fetch();
    }
    if (!$owned) {
        json_out([
            'locked'   => true,
            'message'  => 'Free preview ended. Unlock this chapter to keep reading.',
            'price'    => $series['novel_price'],
            'series'   => ['id' => $series['id'], 'title' => $series['title']]
        ], 402); // 402 Payment Required
    }
}

// 🧭 NEW: prev / next chapter + full chapter list for the reader nav
$st = $pdo->prepare('SELECT id, chapter_number FROM chapters
                     WHERE series_id = ? AND chapter_number < ?
                     ORDER BY chapter_number DESC LIMIT 1');
$st->execute([$chapter['series_id'], $chapter['chapter_number']]);
$prevCh = $st->fetch();

$st = $pdo->prepare('SELECT id, chapter_number FROM chapters
                     WHERE series_id = ? AND chapter_number > ?
                     ORDER BY chapter_number ASC LIMIT 1');
$st->execute([$chapter['series_id'], $chapter['chapter_number']]);
$nextCh = $st->fetch();

$st = $pdo->prepare('SELECT id, chapter_number, chapter_number AS number, title FROM chapters
                     WHERE series_id = ? ORDER BY chapter_number ASC');
$st->execute([$chapter['series_id']]);
$allChapters = $st->fetchAll();

json_out(['data' => [
    'id'          => $chapter['id'],
    'series_id'   => (int)$chapter['series_id'],
    'number'      => $chapter['chapter_number'],
    'title'       => $chapter['title'],
    'is_free'     => (bool)$chapter['is_free'],
    'external_id' => $chapter['external_id'] ?? null,   // ✅ MangaDex UUID for live pages
    'pages'       => $chapter['pages_json'] ? json_decode($chapter['pages_json'], true) : null,
    'content'     => $chapter['content'],

    // 🧭 NEW: nav data — reader.php's buildNav() reads these
    'prev'     => $prevCh ?: null,
    'next'     => $nextCh ?: null,
    'chapters' => $allChapters,
]]);
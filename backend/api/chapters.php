<?php
require_once __DIR__ . '/../db.php';
$pdo = db();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM chapters WHERE id = ?');
$stmt->execute([$id]);
$chapter = $stmt->fetch();
if (!$chapter) {
    json_out(['error' => 'Not found'], 404);
}

$s = $pdo->prepare('SELECT * FROM series WHERE id = ?');
$s->execute([$chapter['series_id']]);
$series = $s->fetch();

// ── Coin lock (Webtoon-style) ──────────────────────────────
$coinPrice = (int)($chapter['coin_price'] ?? 0);
$isFree    = !empty($chapter['is_free']) || $coinPrice <= 0;

if (!$isFree) {
    $user = current_user();

    if (!$user) {
        json_out([
            'locked'     => true,
            'reason'     => 'login',
            'message'    => 'Log in to unlock this chapter.',
            'coin_price' => $coinPrice,
            'chapter_id' => (int)$chapter['id'],
            'series'     => [
                'id'    => (int)$series['id'],
                'title' => $series['title']
            ]
        ], 402);
    }

    $uid = (int)$user['id'];

    // Already unlocked with coins?
    $st = $pdo->prepare(
        'SELECT 1 FROM chapter_unlocks WHERE user_id = ? AND chapter_id = ?'
    );
    $st->execute([$uid, $chapter['id']]);
    $unlocked = (bool)$st->fetch();

    if (!$unlocked) {
        $st = $pdo->prepare('SELECT balance FROM wallets WHERE user_id = ?');
        $st->execute([$uid]);
        $balance = (int)($st->fetchColumn() ?: 0);

        json_out([
            'locked'     => true,
            'reason'     => 'coins',
            'message'    => 'Unlock this chapter with coins.',
            'coin_price' => $coinPrice,
            'balance'    => $balance,
            'chapter_id' => (int)$chapter['id'],
            'series'     => [
                'id'    => (int)$series['id'],
                'title' => $series['title']
            ]
        ], 402);
    }
}

// ── Prev / next / chapter list ─────────────────────────────
$st = $pdo->prepare(
    'SELECT id, chapter_number FROM chapters
     WHERE series_id = ? AND chapter_number < ?
     ORDER BY chapter_number DESC LIMIT 1'
);
$st->execute([$chapter['series_id'], $chapter['chapter_number']]);
$prevCh = $st->fetch();

$st = $pdo->prepare(
    'SELECT id, chapter_number FROM chapters
     WHERE series_id = ? AND chapter_number > ?
     ORDER BY chapter_number ASC LIMIT 1'
);
$st->execute([$chapter['series_id'], $chapter['chapter_number']]);
$nextCh = $st->fetch();

$st = $pdo->prepare(
    'SELECT id, chapter_number, chapter_number AS number, title, is_free, coin_price
     FROM chapters
     WHERE series_id = ?
     ORDER BY chapter_number ASC'
);
$st->execute([$chapter['series_id']]);
$allChapters = $st->fetchAll();

json_out(['data' => [
    'id'              => $chapter['id'],
    'series_id'       => (int)$chapter['series_id'],
    'number'          => $chapter['chapter_number'],
    'title'           => $chapter['title'],
    'is_free'         => (bool)$chapter['is_free'],
    'coin_price'      => (int)($chapter['coin_price'] ?? 0),
    'external_id'     => $chapter['external_id'] ?? null,
    'pages'           => $chapter['pages_json'] ? json_decode($chapter['pages_json'], true) : null,
    'content'         => $chapter['content'],
    'external_source' => $series['external_source'] ?? null,
    'prev'            => $prevCh ?: null,
    'next'            => $nextCh ?: null,
    'chapters'        => $allChapters,
]]);
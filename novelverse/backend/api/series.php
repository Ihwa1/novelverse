<?php ini_set('display_errors', 1); error_reporting(E_ALL);
require_once __DIR__ . '/../db.php';
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

// Small helper to call the MangaDex API (same style as mangadex.php)
function md_get(string $path, array $params = []): array {
    $url = 'https://api.mangadex.org' . $path . ($params ? '?' . http_build_query($params) : '');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'NovelVerse/1.0',
        CURLOPT_HTTPHEADER => ['Accept: application/json']
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true) ?: [];
}

if ($method === 'GET') {
    if (isset($_GET['slug'])) {
        $stmt = $pdo->prepare('SELECT * FROM series WHERE slug = ?');
        $stmt->execute([$_GET['slug']]);
        $series = $stmt->fetch();
        if (!$series) json_out(['error' => 'Not found'], 404);
        $ch = $pdo->prepare('SELECT id, chapter_number, title, is_free, release_date FROM chapters WHERE series_id = ? ORDER BY CAST(chapter_number AS UNSIGNED) ASC');
        $ch->execute([$series['id']]);
        $series['chapters'] = $ch->fetchAll();

        // does current user own this novel?
        $series['owned'] = false;
        if ($u = current_user()) {
            $p = $pdo->prepare("SELECT 1 FROM purchases WHERE user_id = ? AND series_id = ? AND status = 'paid'");
            $p->execute([$u['id'], $series['id']]);
            $series['owned'] = (bool)$p->fetch();
        }
        json_out(['data' => $series]);
    }

    // Browse: ?type=manhwa&q=...&page=1
    $sql = 'SELECT * FROM series WHERE 1';
    $params = [];
    if (!empty($_GET['type'])) { $sql .= ' AND type = ?'; $params[] = $_GET['type']; }
    if (!empty($_GET['q']))    { $sql .= ' AND title LIKE ?'; $params[] = '%' . $_GET['q'] . '%'; }
    $sql .= ' ORDER BY created_at DESC LIMIT 60';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    json_out(['data' => $stmt->fetchAll()]);
}

// POST — add a series (admin/import from MangaDex)
if ($method === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true);

    try {
        // Layer 1: unique slug key rejects duplicates at the DB level
        $stmt = $pdo->prepare('INSERT INTO series (title, slug, type, description, cover_url, author, status, is_novel, novel_price, free_chapters, external_source, external_id)
                               VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $in['title'], slugify($in['title']), $in['type'], $in['description'] ?? '',
            $in['cover'] ?? null, $in['author'] ?? null, $in['status'] ?? 'ongoing',
            ($in['type'] === 'novel') ? 1 : 0,
            $in['novel_price'] ?? 0, $in['free_chapters'] ?? 3,
            $in['external_source'] ?? null, $in['external_id'] ?? null
        ]);
        $seriesId = $pdo->lastInsertId();

    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            json_out([
                'status'  => 'exists',
                'message' => 'Already in NovelVerse!',
                'data'    => ['slug' => slugify($in['title'])]
            ], 200);
        } else {
            json_out(['error' => $e->getMessage()], 500);
        }
    }

    // ✅ NEW: Option B hybrid — import chapters from MangaDex into our DB
    $importedChapters = 0;
    if (($in['external_source'] ?? '') === 'mangadex' && !empty($in['external_id'])) {
        $feed = md_get('/manga/' . $in['external_id'] . '/feed', [
            'translatedLanguage' => ['en'],
            'order[chapter]' => 'asc',
            'limit' => 500
        ]);

        $freeCount = (int)($in['free_chapters'] ?? 3);
        $ins = $pdo->prepare('INSERT INTO chapters (series_id, chapter_number, title, is_free, external_id, release_date)
                              VALUES (?,?,?,?,?,?)');

        foreach ($feed['data'] ?? [] as $c) {
            $num = $c['attributes']['chapter'] ?? null;
            if ($num === null || $num === '') continue; // skip oneshots/no-number entries

            try {
                $ins->execute([
                    $seriesId,
                    $num,
                    $c['attributes']['title'] ?? '',
                    ((int)$num <= $freeCount) ? 1 : 0,   // first N free, rest locked 🔒
                    $c['id'],                             // MangaDex UUID for live page fetching
                    $c['attributes']['publishAt'] ?? date('Y-m-d H:i:s')
                ]);
                $importedChapters++;
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') throw $e; // ignore dup chapters, rethrow real errors
            }
        }
    }

    json_out([
        'data' => [
            'id' => $seriesId,
            'slug' => slugify($in['title']),
            'chapters_imported' => $importedChapters
        ]
    ], 201);
}
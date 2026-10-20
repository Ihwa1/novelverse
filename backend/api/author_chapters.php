
<?php
require_once __DIR__ . '/../db.php';

$user = require_author();
$pdo = db();
$userId = (int) $user['id'];

function getOwnedSeries(PDO $pdo, int $seriesId, int $userId): bool {
    $stmt = $pdo->prepare(
        'SELECT id FROM series WHERE id = ? AND created_by = ?'
    );
    $stmt->execute([$seriesId, $userId]);

    return (bool) $stmt->fetch();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $seriesId = (int) ($_GET['series_id'] ?? 0);

    if ($seriesId < 1 || !getOwnedSeries($pdo, $seriesId, $userId)) {
        json_out(['error' => 'Series not found or access denied'], 404);
    }

    $stmt = $pdo->prepare(
        'SELECT id, series_id, chapter_number, title, is_free,
                release_date, views
         FROM chapters
         WHERE series_id = ?
         ORDER BY release_date DESC, id DESC'
    );
    $stmt->execute([$seriesId]);

    json_out(['data' => $stmt->fetchAll()]);
}

if ($method !== 'POST') {
    json_out(['error' => 'Method not allowed'], 405);
}

$in = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $in['action'] ?? 'create';

if ($action === 'delete') {
    $chapterId = (int) ($in['id'] ?? 0);

    $stmt = $pdo->prepare(
        'SELECT c.id
         FROM chapters c
         INNER JOIN series s ON s.id = c.series_id
         WHERE c.id = ? AND s.created_by = ?'
    );
    $stmt->execute([$chapterId, $userId]);

    if (!$stmt->fetch()) {
        json_out(['error' => 'Chapter not found or access denied'], 404);
    }

    $stmt = $pdo->prepare('DELETE FROM chapters WHERE id = ?');
    $stmt->execute([$chapterId]);

    json_out(['data' => ['deleted' => true]]);
}

$seriesId = (int) ($in['series_id'] ?? 0);

if ($seriesId < 1 || !getOwnedSeries($pdo, $seriesId, $userId)) {
    json_out(['error' => 'You can only publish chapters to your own series'], 403);
}

$chapterNumber = trim((string) ($in['chapter_number'] ?? '1'));
$title = trim((string) ($in['title'] ?? ''));
$content = trim((string) ($in['content'] ?? ''));

if ($chapterNumber === '' || strlen($chapterNumber) > 20) {
    json_out(['error' => 'Enter a valid chapter number (maximum 20 characters)'], 422);
}

$rawPages = $in['pages'] ?? [];

if (is_string($rawPages)) {
    $rawPages = preg_split('/\r\n|\r|\n/', $rawPages);
}

if (!is_array($rawPages)) {
    json_out(['error' => 'Pages must be a list of image URLs'], 422);
}

$pages = [];

foreach ($rawPages as $page) {
    if (!is_string($page)) {
        continue;
    }

    $page = trim($page);

    if ($page === '') {
        continue;
    }

    $url = filter_var($page, FILTER_VALIDATE_URL);
    $scheme = strtolower((string) parse_url($page, PHP_URL_SCHEME));

    if (!$url || !in_array($scheme, ['http', 'https'], true)) {
        json_out(['error' => 'Each page must be a valid HTTP or HTTPS URL'], 422);
    }

    $pages[] = $page;
}

$isFree = filter_var(
    $in['is_free'] ?? true,
    FILTER_VALIDATE_BOOLEAN
) ? 1 : 0;

$pagesJson = $pages ? json_encode($pages) : null;
$textContent = $content !== '' ? $content : null;

$stmt = $pdo->prepare(
    'INSERT INTO chapters
     (series_id, chapter_number, title, is_free, pages_json, content)
     VALUES (?, ?, ?, ?, ?, ?)'
);

$stmt->execute([
    $seriesId,
    $chapterNumber,
    $title !== '' ? $title : null,
    $isFree,
    $pagesJson,
    $textContent
]);

json_out([
    'data' => [
        'id' => (int) $pdo->lastInsertId(),
        'series_id' => $seriesId,
        'chapter_number' => $chapterNumber,
        'pages' => count($pages),
        'created' => true
    ]
], 201);

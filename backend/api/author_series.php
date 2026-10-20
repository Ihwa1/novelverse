
<?php
require_once __DIR__ . '/../db.php';

$user = require_author();
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT * FROM series
         WHERE created_by = ?
         ORDER BY id DESC'
    );
    $stmt->execute([(int) $user['id']]);

    json_out(['data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'Method not allowed'], 405);
}

$in = json_decode(file_get_contents('php://input'), true) ?? [];

$title = trim($in['title'] ?? '');

if ($title === '') {
    json_out(['error' => 'Title is required'], 422);
}

$type = $in['type'] ?? 'manga';
$allowedTypes = ['manga', 'manhwa', 'manhua', 'novel'];

if (!in_array($type, $allowedTypes, true)) {
    json_out(['error' => 'Invalid series type'], 422);
}

$status = $in['status'] ?? 'ongoing';
$allowedStatuses = ['ongoing', 'completed', 'hiatus'];

if (!in_array($status, $allowedStatuses, true)) {
    json_out(['error' => 'Invalid series status'], 422);
}

$slugBase = slugify($in['slug'] ?? '') ?: slugify($title);

if ($slugBase === '') {
    json_out(['error' => 'A valid title is required'], 422);
}

// Ensure this new series has a unique slug.
$slug = $slugBase;
$suffix = 2;

while (true) {
    $check = $pdo->prepare('SELECT id FROM series WHERE slug = ?');
    $check->execute([$slug]);

    if (!$check->fetch()) {
        break;
    }

    $slug = $slugBase . '-' . $suffix;
    $suffix++;
}

$description = trim($in['description'] ?? '');
$coverUrl = trim($in['cover_url'] ?? '');
$isNovel = $type === 'novel' ? 1 : 0;
$novelPrice = max(0, (float) ($in['novel_price'] ?? 0));

$stmt = $pdo->prepare(
    'INSERT INTO series
     (title, slug, type, status, description, cover_url,
      is_novel, novel_price, created_by)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$stmt->execute([
    $title,
    $slug,
    $type,
    $status,
    $description !== '' ? $description : null,
    $coverUrl !== '' ? $coverUrl : null,
    $isNovel,
    $novelPrice,
    (int) $user['id']
]);

json_out([
    'data' => [
        'id' => (int) $pdo->lastInsertId(),
        'title' => $title,
        'slug' => $slug,
        'created' => true
    ]
], 201);

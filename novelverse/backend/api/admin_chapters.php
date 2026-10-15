<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
require_admin(); 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id, series_id, chapter_number, title, is_free
                           FROM chapters WHERE series_id = ? ORDER BY chapter_number');
    $stmt->execute([(int)($_GET['series_id'] ?? 0)]);
    json_out(['data' => $stmt->fetchAll()]);
}

$in = json_decode(file_get_contents('php://input'), true) ?? [];

if (($in['action'] ?? '') === 'delete') {
    $pdo->prepare('DELETE FROM chapters WHERE id = ?')->execute([(int)($in['id'] ?? 0)]);
    json_out(['data' => ['deleted' => true]]);
}

// One URL per line in the textarea → clean pages_json automatically 🪄
$pages = array_values(array_filter(array_map('trim', explode("\n", $in['pages'] ?? ''))));

$stmt = $pdo->prepare('INSERT INTO chapters (series_id, chapter_number, title, is_free, pages_json, content)
                       VALUES (?,?,?,?,?,?)');
$stmt->execute([
    (int)($in['series_id'] ?? 0),
    (int)($in['chapter_number'] ?? 1),
    trim($in['title'] ?? ''),
    !empty($in['is_free']) ? 1 : 0,
    $pages ? json_encode($pages) : null,
    trim($in['content'] ?? '') ?: null,
]);
json_out(['data' => ['id' => (int)$pdo->lastInsertId(), 'created' => true, 'pages' => count($pages)]], 201);
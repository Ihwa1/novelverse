<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
require_admin();
 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rows = $pdo->query('SELECT * FROM series ORDER BY id DESC')->fetchAll();
    json_out(['data' => $rows]);
}

$in = json_decode(file_get_contents('php://input'), true) ?? [];
$id = (int)($in['id'] ?? 0);

$fields = [
    'title'       => trim($in['title'] ?? ''),
    'slug' => slugify(trim($in['slug'] ?? '') ?: ($in['title'] ?? '')),
    'type'        => in_array($in['type'] ?? '', ['manga','manhwa','manhua','novel']) ? $in['type'] : 'manga',
    'status'      => trim($in['status'] ?? 'ongoing'),
    'description' => trim($in['description'] ?? ''),
    'cover_url'   => trim($in['cover_url'] ?? ''),
    'is_novel'    => !empty($in['is_novel']) ? 1 : 0,
    'novel_price' => (float)($in['novel_price'] ?? 0),
];
if ($fields['title'] === '') json_out(['error' => 'Title is required'], 422);

$cols = array_keys($fields);
$marks = rtrim(str_repeat('?, ', count($cols)), ', ');

if ($id) {
    $set = implode(' = ?, ', $cols) . ' = ?';
    $pdo->prepare("UPDATE series SET $set WHERE id = ?")
        ->execute([...array_values($fields), $id]);
    json_out(['data' => ['id' => $id, 'updated' => true]]);
}

$pdo->prepare("INSERT INTO series (" . implode(', ', $cols) . ") VALUES ($marks)")
    ->execute(array_values($fields));
json_out(['data' => ['id' => (int)$pdo->lastInsertId(), 'created' => true]], 201);
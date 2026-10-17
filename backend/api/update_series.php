<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

// 🔐 must be logged in as author/admin — using current_user() like upload.php
$u = current_user();
if (!$u || (($u['role'] ?? '') !== 'author' && (int)($u['is_admin'] ?? 0) !== 1))
    json_out(['error' => 'Authors only'], 403);

$pdo = db();
$seriesId = (int)($_POST['series_id'] ?? 0);

// 🔒 ownership check — same rule as manage.php
$st = $pdo->prepare('SELECT * FROM series WHERE id = ?');
$st->execute([$seriesId]);
$series = $st->fetch();
if (!$series || ((int)$series['created_by'] !== (int)$u['id'] && (int)($u['is_admin'] ?? 0) !== 1))
    json_out(['error' => 'Series not found or not yours'], 404);

// ✏️ collect updatable fields
$title    = trim($_POST['title'] ?? '');
$type     = trim($_POST['type'] ?? '');
$status   = trim($_POST['status'] ?? 'ongoing');
$author   = trim($_POST['author_name'] ?? '');
$desc     = trim($_POST['description'] ?? '');
$freeCh   = max(0, (int)($_POST['free_chapters'] ?? 0));

if ($title === '')
    json_out(['error' => 'Title is required'], 400);

$st = $pdo->prepare('UPDATE series SET title = ?, type = ?, status = ?, author = ?, description = ?, free_chapters = ? WHERE id = ?');
$st->execute([$title, $type, $status, $author, $desc, $freeCh, $seriesId]);

// 🖼️ optional new cover — same save_image pattern as upload.php
if (!empty($_FILES['cover']['tmp_name']) && is_uploaded_file($_FILES['cover']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
        json_out(['error' => 'Only jpg / png / webp / gif images allowed'], 422);

    $dir = __DIR__ . '/../../public/uploads/covers/';
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $fname = 'cover_' . $seriesId . '_' . time() . '.' . $ext;
    if (!move_uploaded_file($_FILES['cover']['tmp_name'], $dir . $fname))
        json_out(['error' => 'Cover upload failed — check folder permissions'], 500);

    // 📝 store full relative path so <img src> works from public/
    $pdo->prepare('UPDATE series SET cover_url = ? WHERE id = ?')->execute(['uploads/covers/' . $fname, $seriesId]);
}

json_out(['ok' => true]);
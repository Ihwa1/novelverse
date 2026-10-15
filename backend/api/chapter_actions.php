<?php
require_once __DIR__ . '/../db.php';   // ⚠️ same path as update_series.php

header('Content-Type: application/json');

$u = current_user();
if (!$u || (($u['role'] ?? '') !== 'author' && (int)($u['is_admin'] ?? 0) !== 1))
    json_out(['error' => 'Authors only'], 403);


$pdo = db();
$chapterId = (int)($_POST['chapter_id'] ?? 0);
$action    = $_POST['action'] ?? '';

// 🔒 ownership check — chapter must belong to a series this author owns
$st = $pdo->prepare('SELECT c.*, s.created_by FROM chapters c JOIN series s ON s.id = c.series_id WHERE c.id = ?');
$st->execute([$chapterId]);
$chapter = $st->fetch();
if (!$chapter || ((int)$chapter['created_by'] !== (int)$u['id'] && (int)($u['is_admin'] ?? 0) !== 1)) {
    json_out(['error' => 'Chapter not found or not yours'], 404);
    exit;
}

if ($action === 'delete') {
    // 🧹 clean up chapter files if they're local uploads
    if (!empty($chapter['pages'])) {
        $pages = json_decode($chapter['pages'], true);
        if (is_array($pages)) {
            foreach ($pages as $p) {
                $f = __DIR__ . '/../../public/uploads/chapters/' . basename($p); // ✅ matches upload.php
                if (is_file($f)) @unlink($f);
            }
        }
    }
    $pdo->prepare('DELETE FROM chapters WHERE id = ?')->execute([$chapterId]);
    echo json_encode(['ok' => true, 'deleted' => true]);
    exit;
}
if ($action === 'toggle') {
    $newVal = ((int)$chapter['is_free'] === 1) ? 0 : 1;
    $pdo->prepare('UPDATE chapters SET is_free = ? WHERE id = ?')->execute([$newVal, $chapterId]);
    echo json_encode(['ok' => true, 'is_free' => (bool)$newVal]);
    exit;
}

if ($action === 'toggle_free') {
    $newVal = (int)$chapter['is_free'] ? 0 : 1;
    $pdo->prepare('UPDATE chapters SET is_free = ? WHERE id = ?')->execute([$newVal, $chapterId]);
    echo json_encode(['ok' => true, 'is_free' => $newVal]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
<?php
require_once __DIR__ . '/../db.php';
$pdo = db();

$user = current_user();
if (!$user) json_out(['error' => 'Login required'], 401);

$in = json_decode(file_get_contents('php://input'), true);
$seriesId  = (int)($in['series_id'] ?? 0);
$chapterId = (int)($in['chapter_id'] ?? 0);
if (!$seriesId || !$chapterId) json_out(['error' => 'Invalid input'], 422);

// Save (or update) the user's latest chapter for this series
$stmt = $pdo->prepare(
    'INSERT INTO reading_progress (user_id, series_id, chapter_id)
     VALUES (?,?,?)
     ON DUPLICATE KEY UPDATE chapter_id = VALUES(chapter_id), updated_at = CURRENT_TIMESTAMP'
);
$stmt->execute([$user['id'], $seriesId, $chapterId]);

// Bonus: bump the view counter on the chapter
$pdo->prepare('UPDATE chapters SET views = views + 1 WHERE id = ?')
    ->execute([$chapterId]);

json_out(['data' => 'ok']);
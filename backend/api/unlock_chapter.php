<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
$user = current_user();

if (!$user) {
    json_out(['error' => 'Login required'], 401);
}

$in = json_decode(file_get_contents('php://input'), true) ?? [];
$chapterId = (int)($in['chapter_id'] ?? 0);
if ($chapterId < 1) {
    json_out(['error' => 'Missing chapter_id'], 400);
}

$st = $pdo->prepare('SELECT * FROM chapters WHERE id = ?');
$st->execute([$chapterId]);
$chapter = $st->fetch();
if (!$chapter) {
    json_out(['error' => 'Chapter not found'], 404);
}

$price = (int)($chapter['coin_price'] ?? 0);
$uid = (int)$user['id'];

// Already free
if ($price <= 0 || !empty($chapter['is_free'])) {
    json_out(['data' => ['ok' => true, 'message' => 'Already free']]);
}

// Already unlocked?
$st = $pdo->prepare('SELECT 1 FROM chapter_unlocks WHERE user_id = ? AND chapter_id = ?');
$st->execute([$uid, $chapterId]);
if ($st->fetch()) {
    json_out(['data' => ['ok' => true, 'message' => 'Already unlocked']]);
}

$pdo->beginTransaction();
try {
    $pdo->prepare('INSERT IGNORE INTO wallets (user_id, balance) VALUES (?, 0)')->execute([$uid]);

    $st = $pdo->prepare('SELECT balance FROM wallets WHERE user_id = ? FOR UPDATE');
    $st->execute([$uid]);
    $balance = (int)$st->fetchColumn();

    if ($balance < $price) {
        $pdo->rollBack();
        json_out([
            'error' => 'Not enough coins',
            'balance' => $balance,
            'coin_price' => $price
        ], 402);
    }

    $pdo->prepare('UPDATE wallets SET balance = balance - ? WHERE user_id = ?')
        ->execute([$price, $uid]);

    $pdo->prepare('INSERT INTO chapter_unlocks (user_id, chapter_id, coins_spent) VALUES (?,?,?)')
        ->execute([$uid, $chapterId, $price]);

    $pdo->commit();

    json_out([
        'data' => [
            'ok' => true,
            'balance' => $balance - $price,
            'coins_spent' => $price
        ]
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_out(['error' => 'Unlock failed'], 500);
}
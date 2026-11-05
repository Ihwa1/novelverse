<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
$user = current_user();
$action = $_GET['action'] ?? 'balance';

if (!$user) {
    json_out(['error' => 'Login required'], 401);
}

$uid = (int)$user['id'];

// Ensure wallet row exists
$pdo->prepare('INSERT IGNORE INTO wallets (user_id, balance) VALUES (?, 0)')->execute([$uid]);

if ($action === 'balance') {
    $st = $pdo->prepare('SELECT balance FROM wallets WHERE user_id = ?');
    $st->execute([$uid]);
    $balance = (int)($st->fetchColumn() ?: 0);
    json_out(['data' => ['balance' => $balance]]);
}

// Demo packs (no PayMongo yet — instant credit for testing)
$PACKS = [
    'starter'  => ['coins' => 50,  'price' => 25],
    'standard' => ['coins' => 120, 'price' => 55],
    'plus'     => ['coins' => 250, 'price' => 100],
    'mega'     => ['coins' => 600, 'price' => 200],
];

if ($action === 'buy' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true) ?? [];
    $packId = $in['pack'] ?? '';
    if (!isset($PACKS[$packId])) {
        json_out(['error' => 'Invalid pack'], 400);
    }
    $coins = $PACKS[$packId]['coins'];

    // DEMO: add coins immediately (replace with PayMongo later)
    $pdo->prepare('UPDATE wallets SET balance = balance + ? WHERE user_id = ?')
        ->execute([$coins, $uid]);

    $st = $pdo->prepare('SELECT balance FROM wallets WHERE user_id = ?');
    $st->execute([$uid]);
    $balance = (int)$st->fetchColumn();

    json_out([
        'data' => [
            'ok' => true,
            'added' => $coins,
            'balance' => $balance,
            'demo' => true,
            'message' => "Demo purchase: +{$coins} coins"
        ]
    ]);
}

json_out(['error' => 'Unknown action'], 400);
<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
$action = $_GET['action'] ?? '';

function paymongo(string $method, string $path, array $body = []): array {
    $ch = curl_init('https://api.paymongo.com' . $path);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $body ? json_encode(['data' => ['attributes' => $body]]) : null,
        CURLOPT_USERPWD => PAYMONGO_SECRET . ':',
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $res;
}

// POST { series_id, method: 'gcash'|'card' } → creates payment intent + checkout
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = require_user();
    $in = json_decode(file_get_contents('php://input'), true);

    $s = $pdo->prepare('SELECT * FROM series WHERE id = ? AND is_novel = 1');
    $s->execute([(int)$in['series_id']]);
    $series = $s->fetch();
    if (!$series) json_out(['error' => 'Novel not found'], 404);

    // Already owned?
    $p = $pdo->prepare("SELECT 1 FROM purchases WHERE user_id = ? AND series_id = ? AND status = 'paid'");
    $p->execute([$user['id'], $series['id']]);
    if ($p->fetch()) json_out(['error' => 'Already purchased'], 409);

    $amount = (int)($series['novel_price'] * 100); // centavos
    $intent = paymongo('POST', '/v1/payment_intents', [
        'amount'               => $amount,
        'payment_method_allowed' => $in['method'] === 'gcash' ? ['gcash'] : ['card'],
        'payment_method_options' => ['card' => ['request_three_d_secure' => 'any']],
        'currency'             => CURRENCY,
        'description'          => 'NovelVerse: ' . $series['title']
    ]);
    if (empty($intent['data'])) json_out(['error' => 'Payment init failed', 'raw' => $intent], 502);
    $pid = $intent['data']['id'];

    $ins = $pdo->prepare("INSERT INTO purchases (user_id, series_id, amount, method, paymongo_intent_id, status)
                          VALUES (?,?,?,?,?,'pending')");
    $ins->execute([$user['id'], $series['id'], $series['novel_price'], $in['method'], $pid]);

    if ($in['method'] === 'gcash') {
        // attach GCash method → returns a redirect checkout URL
        $attach = paymongo('POST', "/v1/payment_intents/$pid/attach", [
            'payment_method' => 'pm_gcash', // PayMongo test GCash method
            'return_url'     => BASE_URL . '/public/novel.php?slug=' . $series['slug'] . '&paid=1'
        ]);
        $url = $attach['data']['attributes']['next_action']['redirect']['url'] ?? null;
        json_out(['data' => ['checkout_url' => $url, 'intent_id' => $pid]]);
    }
    // Card: frontend completes with PayMongo Elements using intent_id (client key)
    json_out(['data' => ['intent_id' => $pid, 'client_key' => $intent['data']['attributes']['client_key']]]);
}

// PayMongo webhook — mark purchase as paid
if ($action === 'webhook' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = json_decode(file_get_contents('php://input'), true);
    $type  = $event['data']['attributes']['type'] ?? '';
    $data  = $event['data']['attributes']['data'] ?? [];

    if (in_array($type, ['payment_intent.succeeded', 'payment.paid'])) {
        $pid = $data['id'] ?? ($data['attributes']['payment_intent_id'] ?? '');
        $upd = $pdo->prepare("UPDATE purchases SET status = 'paid' WHERE paymongo_intent_id = ?");
        $upd->execute([$pid]);
    }
    http_response_code(200);
    echo json_encode(['received' => true]);
    exit;
}
<?php
require_once __DIR__ . '/config.php';

// JSON headers only for API responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

function json_out($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    // ✅ CHANGED: added role so author-only pages/APIs can check it
    $stmt = db()->prepare('SELECT id, username, email, role, is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;

}

function require_user(): array {
    $u = current_user();
    if (!$u) json_out(['error' => 'Login required'], 401);
    return $u;
}

function slugify(string $s): string {
    return trim(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $s)), '-');
}
function require_admin(): array {
    $u = require_user();
    if (empty($u['is_admin'])) json_out(['error' => 'Admins only'], 403);
    return $u;
}
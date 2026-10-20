<?php
require_once __DIR__ . '/../../backend/config.php';

// Simple DB connection for public pages (no JSON headers)
function pub_db(): PDO {
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

// Get logged-in user (server-side)
$nv_user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = pub_db()->prepare('SELECT id, username, role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $nv_user = $stmt->fetch() ?: null;
}
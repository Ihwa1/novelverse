<?php
require_once __DIR__ . '/../../backend/config.php';

// Simple DB connection for public pages (no JSON headers).
function pub_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST .
            ';dbname=' . DB_NAME .
            ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    return $pdo;
}

// Get the logged-in user from the server-side session.
$nv_user = null;

if (!empty($_SESSION['user_id'])) {
    $stmt = pub_db()->prepare(
        'SELECT id, username, role, is_admin
         FROM users
         WHERE id = ?
         LIMIT 1'
    );

    $stmt->execute([$_SESSION['user_id']]);
    $nv_user = $stmt->fetch() ?: null;

    // Clear the session user ID if the account no longer exists.
    if ($nv_user === null) {
        unset($_SESSION['user_id']);
    }
}

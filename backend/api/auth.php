<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
$action = $_GET['action'] ?? '';

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true);
    $email = trim($in['email'] ?? '');

    if (empty($in['username']) || empty($email) || strlen($in['password'] ?? '') < 6)
        json_out(['error' => 'Invalid input (password min 6 chars)'], 422);

    // ✅ NEW: real email format check — must have name@domain.tld
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        json_out(['error' => 'Please enter a valid email address (e.g. name@gmail.com)'], 422);

    // Public registration always creates a Reader account.
    // Never trust a role submitted by the client.
    $role = 'reader';

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, role) VALUES (?,?,?,?)');
        $stmt->execute([
            $in['username'],
            $email,
            password_hash($in['password'], PASSWORD_DEFAULT),
            $role
        ]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        json_out(['data' => ['id' => $_SESSION['user_id'], 'username' => $in['username'], 'role' => $role]], 201);
    } catch (PDOException $e) {
        json_out(['error' => 'Username or email already taken'], 409);
    }
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true);
    $identifier = trim($in['email'] ?? '');

    // ✅ NEW: if they typed something with @, it must be a VALID email format
    if ($identifier !== '' && str_contains($identifier, '@') && !filter_var($identifier, FILTER_VALIDATE_EMAIL))
        json_out(['error' => 'That email looks invalid — check the format (e.g. name@gmail.com)'], 422);

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($in['password'] ?? '', $user['password_hash']))
        json_out(['error' => 'Invalid credentials'], 401);
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    json_out(['data' => ['id' => $user['id'], 'username' => $user['username']]]);
}

if ($action === 'me') {
    $u = current_user();
    json_out(['data' => $u]);
}

if ($action === 'logout') {
    session_destroy();
    json_out(['data' => 'ok']);
}
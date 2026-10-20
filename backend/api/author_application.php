
<?php
require_once __DIR__ . '/../db.php';

$pdo = db();
$action = $_GET['action'] ?? '';

/*
|--------------------------------------------------------------------------
| Submit an Author Application
|--------------------------------------------------------------------------
*/
if ($action === 'apply' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) {
        json_out(['error' => 'Please log in before applying.'], 401);
    }

    $in = json_decode(file_get_contents('php://input'), true);

    if (!is_array($in)) {
        json_out(['error' => 'Invalid request data.'], 400);
    }

    $displayName = trim($in['display_name'] ?? '');
    $bio = trim($in['bio'] ?? '');
    $sampleUrl = trim($in['sample_work_url'] ?? '');

    if ($displayName === '' || $bio === '') {
        json_out([
            'error' => 'Please enter your author display name and bio.'
        ], 422);
    }

    if (strlen($displayName) > 100) {
        json_out([
            'error' => 'Display name must not exceed 100 characters.'
        ], 422);
    }

    if (strlen($bio) < 30) {
        json_out([
            'error' => 'Your bio must contain at least 30 characters.'
        ], 422);
    }

    if (strlen($bio) > 5000) {
        json_out([
            'error' => 'Your bio must not exceed 5,000 characters.'
        ], 422);
    }

    if (strlen($sampleUrl) > 255) {
        json_out([
            'error' => 'The sample work URL is too long.'
        ], 422);
    }

    if (
        $sampleUrl !== '' &&
        (
            !filter_var($sampleUrl, FILTER_VALIDATE_URL) ||
            !in_array(
                strtolower(parse_url($sampleUrl, PHP_URL_SCHEME) ?? ''),
                ['http', 'https'],
                true
            )
        )
    ) {
        json_out([
            'error' => 'Please enter a valid HTTP or HTTPS sample work URL.'
        ], 422);
    }

    // Confirm the logged-in account exists and is eligible to apply.
    $stmt = $pdo->prepare(
        'SELECT id, role, is_admin FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        json_out(['error' => 'Your account could not be verified. Please log in again.'], 401);
    }

    if ((int)$user['is_admin'] === 1) {
        json_out([
            'error' => 'Administrator accounts do not need to apply for Author access.'
        ], 403);
    }

    if ($user['role'] === 'author') {
        json_out([
            'error' => 'Your account already has Author access.'
        ], 409);
    }

    // Prevent duplicate pending applications and applications from existing Authors.
    $stmt = $pdo->prepare(
        "SELECT id, status
         FROM author_applications
         WHERE user_id = ?
         ORDER BY id DESC
         LIMIT 1"
    );
    $stmt->execute([$user['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing && $existing['status'] === 'pending') {
        json_out([
            'error' => 'You already have a pending Author application.'
        ], 409);
    }

    if ($existing && $existing['status'] === 'approved') {
        json_out([
            'error' => 'Your Author application has already been approved.'
        ], 409);
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO author_applications
                (user_id, display_name, bio, sample_work_url, status)
             VALUES (?, ?, ?, ?, 'pending')"
        );

        $stmt->execute([
            $user['id'],
            $displayName,
            $bio,
            $sampleUrl !== '' ? $sampleUrl : null
        ]);

        json_out([
            'data' => [
                'id' => $pdo->lastInsertId(),
                'status' => 'pending',
                'message' => 'Your Author application has been submitted for review.'
            ]
        ], 201);

    } catch (PDOException $e) {
        error_log('Author application error: ' . $e->getMessage());

        json_out([
            'error' => 'Unable to submit your application right now. Please try again.'
        ], 500);
    }
}

/*
|--------------------------------------------------------------------------
| View the Logged-in User's Latest Application
|--------------------------------------------------------------------------
*/
if ($action === 'mine' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_SESSION['user_id'])) {
        json_out(['error' => 'Please log in to view your application.'], 401);
    }

    $stmt = $pdo->prepare(
        "SELECT id, display_name, bio, sample_work_url,
                status, review_note, reviewed_at, created_at
         FROM author_applications
         WHERE user_id = ?
         ORDER BY id DESC
         LIMIT 1"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    json_out(['data' => $application ?: null]);
}

/*
|--------------------------------------------------------------------------
| Unsupported Action
|--------------------------------------------------------------------------
*/
json_out(['error' => 'Invalid action or request method.'], 404);

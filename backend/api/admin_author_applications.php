
<?php
require_once __DIR__ . '/../db.php';

$pdo = db();
require_admin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("
        SELECT
            aa.id,
            aa.user_id,
            aa.display_name,
            aa.bio,
            aa.sample_work_url,
            aa.status,
            aa.review_note,
            aa.created_at,
            u.username,
            u.email,
            u.role
        FROM author_applications aa
        INNER JOIN users u ON u.id = aa.user_id
        ORDER BY
            CASE WHEN aa.status = 'pending' THEN 0 ELSE 1 END,
            aa.created_at DESC
    ");

    json_out(['data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'Method not allowed'], 405);
}

$in = json_decode(file_get_contents('php://input'), true) ?? [];

$id = (int) ($in['id'] ?? 0);
$action = $in['action'] ?? '';
$reviewNote = trim($in['review_note'] ?? '');

if ($id < 1) {
    json_out(['error' => 'A valid application ID is required'], 400);
}

if (!in_array($action, ['approve', 'reject'], true)) {
    json_out(['error' => 'Action must be approve or reject'], 400);
}

if (mb_strlen($reviewNote) > 5000) {
    json_out(['error' => 'Review note must not exceed 5000 characters'], 400);
}

// Support common session layouts for the reviewing admin.
$reviewerId = $_SESSION['user_id']
    ?? ($_SESSION['user']['id'] ?? null);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT id, user_id, status
        FROM author_applications
        WHERE id = ?
        FOR UPDATE
    ");
    $stmt->execute([$id]);
    $application = $stmt->fetch();

    if (!$application) {
        $pdo->rollBack();
        json_out(['error' => 'Application not found'], 404);
    }

    if ($application['status'] !== 'pending') {
        $pdo->rollBack();
        json_out(['error' => 'This application has already been reviewed'], 409);
    }

    if ($action === 'approve') {
        $stmt = $pdo->prepare("
            UPDATE users
            SET role = 'author'
            WHERE id = ? AND is_admin = 0
        ");
        $stmt->execute([$application['user_id']]);

        if ($stmt->rowCount() !== 1) {
            $pdo->rollBack();
            json_out([
                'error' => 'Could not approve this account. Check its current role.'
            ], 409);
        }

        $newStatus = 'approved';
    } else {
        $newStatus = 'rejected';
    }

    $stmt = $pdo->prepare("
        UPDATE author_applications
        SET status = ?,
            reviewed_by = ?,
            review_note = ?,
            reviewed_at = CURRENT_TIMESTAMP
        WHERE id = ? AND status = 'pending'
    ");

    $stmt->execute([
        $newStatus,
        $reviewerId,
        $reviewNote !== '' ? $reviewNote : null,
        $id
    ]);

    if ($stmt->rowCount() !== 1) {
        $pdo->rollBack();
        json_out(['error' => 'The application could not be updated'], 409);
    }

    $pdo->commit();

    json_out([
        'data' => [
            'id' => $id,
            'status' => $newStatus,
            'message' => $newStatus === 'approved'
                ? 'Author application approved.'
                : 'Author application rejected.'
        ]
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Author application review failed: ' . $e->getMessage());
    json_out(['error' => 'A server error occurred while reviewing the application'], 500);
}


<?php
require_once __DIR__ . '/../db.php';

$pdo = db();
require_admin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_out(['error' => 'Method not allowed'], 405);
}

try {
    $stmt = $pdo->query("
        SELECT
            (SELECT COUNT(*) FROM users
                WHERE is_admin = 0)
                AS total_users,

            (SELECT COUNT(*) FROM users
                WHERE role = 'author')
                AS total_authors,

            (SELECT COUNT(*) FROM series)
                AS total_series,

            (SELECT COUNT(*) FROM chapters)
                AS total_chapters,

            (SELECT COUNT(*) FROM author_applications
                WHERE status = 'pending')
                AS pending_applications
    ");

    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    foreach ($stats as $key => $value) {
        $stats[$key] = (int) $value;
    }

    json_out(['data' => $stats]);
} catch (Throwable $e) {
    error_log('Admin statistics error: ' . $e->getMessage());

    json_out([
        'error' => 'Unable to load dashboard statistics.'
    ], 500);
}

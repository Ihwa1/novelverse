<?php ini_set('display_errors', 1); error_reporting(E_ALL);
require_once __DIR__ . '/../db.php';
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

// ---------- Feature detection (paste-safe) ----------
$hasProgress = false;   // reading_progress table
$hasAvatar   = false;   // users.avatar_url column
try {
    $r = $pdo->query('SELECT 1 FROM reading_progress LIMIT 1');
    $hasProgress = ($r !== false);
} catch (Exception $e) {}
try {
    $r = $pdo->query('SELECT avatar_url FROM users LIMIT 1');
    $hasAvatar = ($r !== false);
} catch (Exception $e) {}

if ($method === 'GET') {

    // ============ CURRENT USER: ?me=1 ============
    if (isset($_GET['me'])) {
        $u = current_user();
        if (!$u) json_out(['error' => 'Not logged in'], 401);
        json_out(['data' => $u]);
    }

    // ============ TOP READERS: ?sort=reading&limit=10 ============
    $sort  = $_GET['sort']    ?? '';
    $limit = min(max((int)($_GET['limit'] ?? 10), 1), 50);

    if ($sort === 'reading') {
        if (!$hasProgress) {
            // No reading_progress table yet → empty leaderboard, no crash
            json_out(['data' => []]);
        }

        $avatarSelect = $hasAvatar ? 'u.avatar_url' : 'NULL AS avatar_url';

        // Rank users by number of chapters they've actually read
        $sql = "SELECT u.id, u.username, $avatarSelect,
                  COUNT(rp.chapter_id) AS chapters_read
                FROM users u
                JOIN reading_progress rp ON rp.user_id = u.id
                WHERE rp.chapter_id IS NOT NULL
                GROUP BY u.id, u.username" . ($hasAvatar ? ', u.avatar_url' : '') . "
                ORDER BY chapters_read DESC, u.username ASC
                LIMIT $limit";
        $rows = $pdo->query($sql)->fetchAll();

        // Normalize for the frontend
        foreach ($rows as &$r) {
            $r['avatar_url'] = $r['avatar_url'] ?? null;
        }
        unset($r);

        json_out(['data' => $rows]);
    }

    // Unknown sort → bad request
    json_out(['error' => 'Unknown sort. Use ?sort=reading'], 400);
}

json_out(['error' => 'Method not allowed'], 405);
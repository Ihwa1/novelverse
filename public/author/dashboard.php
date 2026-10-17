
<?php
require_once __DIR__ . '/../includes/init.php';

// Allow Authors and Admins only.
if (
    !$nv_user ||
    (
        ($nv_user['role'] ?? '') !== 'author' &&
        (int)($nv_user['is_admin'] ?? 0) !== 1
    )
) {
    header('Location: /novelverse/public/index.php');
    exit;
}

$pdo = pub_db();

// Admins can see all series; authors see only their own.
$isAdmin = (int)($nv_user['is_admin'] ?? 0) === 1;

if ($isAdmin) {
    $where = '';
    $params = [];
} else {
    $where = 'WHERE s.created_by = ?';
    $params = [$nv_user['id']];
}

// Get series count, chapter count, and total views.
$sql = "
    SELECT
        COUNT(DISTINCT s.id) AS total_series,
        COUNT(DISTINCT c.id) AS total_chapters,
        COALESCE(SUM(DISTINCT s.views), 0) AS total_views
    FROM series s
    LEFT JOIN chapters c ON c.series_id = s.id
    $where
";

// Avoid SUM(DISTINCT views), which can undercount if series share the same views.
$sql = "
    SELECT
        COUNT(*) AS total_series,
        COALESCE(SUM(s.views), 0) AS total_views
    FROM series s
    $where
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stats = $stmt->fetch() ?: [
    'total_series' => 0,
    'total_views' => 0
];

// Get chapter count separately to avoid duplicating series views.
$chapterSql = "
    SELECT COUNT(c.id)
    FROM chapters c
    INNER JOIN series s ON s.id = c.series_id
    $where
";

$stmt = $pdo->prepare($chapterSql);
$stmt->execute($params);
$totalChapters = (int)$stmt->fetchColumn();

// Get series list and chapter counts.
$listSql = "
    SELECT
        s.id,
        s.title,
        s.slug,
        s.type,
        s.status,
        s.cover_url,
        s.views,
        s.created_at,
        COUNT(c.id) AS chapter_count
    FROM series s
    LEFT JOIN chapters c ON c.series_id = s.id
    $where
    GROUP BY
        s.id, s.title, s.slug, s.type, s.status,
        s.cover_url, s.views, s.created_at
    ORDER BY s.created_at DESC
";

$stmt = $pdo->prepare($listSql);
$stmt->execute($params);
$seriesList = $stmt->fetchAll();

$pageTitle = 'Author Dashboard';
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center
                flex-wrap gap-3 mb-4">
        <div>
            <h1 class="fw-bold">Author Dashboard</h1>
            <p class="text-muted mb-0">
                Welcome, <?= htmlspecialchars($nv_user['username']) ?>!
                Manage your series and chapters.
            </p>
        </div>

        <a href="create-series.php" class="btn btn-nv">
            <i class="bi bi-plus-circle me-1"></i>
            Create Series
        </a>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-book fs-2"></i>
                    <p class="text-muted mb-1 mt-3">My Series</p>
                    <h2 class="fw-bold">
                        <?= (int)$stats['total_series'] ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-journal-text fs-2"></i>
                    <p class="text-muted mb-1 mt-3">Total Chapters</p>
                    <h2 class="fw-bold">
                        <?= $totalChapters ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-eye fs-2"></i>
                    <p class="text-muted mb-1 mt-3">Total Views</p>
                    <h2 class="fw-bold">
                        <?= number_format((int)$stats['total_views']) ?>
                    </h2>
                </div>
            </div>
        </div>

    </div>

    <!-- Series Table -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between
                        align-items-center flex-wrap gap-2 mb-3">
                <h4 class="fw-bold mb-0">
                    <?= $isAdmin ? 'All Series' : 'My Series' ?>
                </h4>

                <span class="text-muted">
                    <?= count($seriesList) ?> series
                </span>
            </div>

            <?php if (!$seriesList): ?>

                <div class="text-center py-5">
                    <i class="bi bi-book fs-1 text-muted"></i>
                    <h5 class="mt-3">No series yet</h5>
                    <p class="text-muted">
                        Create your first series to start publishing.
                    </p>
                    <a href="create-series.php" class="btn btn-nv">
                        Create Your First Series
                    </a>
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Series</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Chapters</th>
                                <th>Views</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($seriesList as $series): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($series['cover_url'])): ?>
                                            <img
                                                src="/novelverse/public/<?= htmlspecialchars(ltrim($series['cover_url'], '/')) ?>"
                                                alt="Cover"
                                                style="width:45px;height:60px;object-fit:cover"
                                                class="rounded"
                                            >
                                        <?php endif; ?>

                                        <div>
                                            <strong>
                                                <?= htmlspecialchars($series['title']) ?>
                                            </strong>
                                            <div class="text-muted small">
                                                <?= htmlspecialchars($series['slug']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?= htmlspecialchars(ucfirst($series['type'])) ?>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars(ucfirst($series['status'] ?? 'ongoing')) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= (int)$series['chapter_count'] ?>
                                </td>

                                <td>
                                    <?= number_format((int)$series['views']) ?>
                                </td>

                                <td>
                                    <a
                                        href="my-series.php"
                                        class="btn btn-sm btn-outline-secondary"
                                    >
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

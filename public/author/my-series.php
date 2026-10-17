
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
$isAdmin = (int)($nv_user['is_admin'] ?? 0) === 1;

// Authors see only their own series.
// Admins can see all series.
if ($isAdmin) {
    $where = '';
    $params = [];
} else {
    $where = 'WHERE s.created_by = ?';
    $params = [$nv_user['id']];
}

$sql = "
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
        s.id, s.title, s.slug, s.type,
        s.status, s.cover_url, s.views, s.created_at
    ORDER BY s.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$seriesList = $stmt->fetchAll();

$pageTitle = 'My Series';
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center
                flex-wrap gap-3 mb-4">
        <div>
            <h1 class="fw-bold">
                <?= $isAdmin ? 'All Series' : 'My Series' ?>
            </h1>
            <p class="text-muted mb-0">
                View your series and manage their chapters.
            </p>
        </div>

        <a href="create-series.php" class="btn btn-nv">
            <i class="bi bi-plus-circle me-1"></i>
            Create Series
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <?php if (empty($seriesList)): ?>

                <div class="text-center py-5">
                    <i class="bi bi-book fs-1 text-muted"></i>
                    <h5 class="mt-3">No series found</h5>
                    <p class="text-muted">
                        Create your first series to get started.
                    </p>
                    <a href="create-series.php" class="btn btn-nv">
                        Create Series
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
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($series['cover_url'])): ?>
                                            <img
                                                src="/novelverse/public/<?= htmlspecialchars(ltrim($series['cover_url'], '/'), ENT_QUOTES, 'UTF-8') ?>"
                                                alt="Series cover"
                                                width="50"
                                                height="70"
                                                class="rounded"
                                                style="object-fit:cover"
                                            >
                                        <?php endif; ?>

                                        <div>
                                            <strong>
                                                <?= htmlspecialchars($series['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </strong>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($series['slug'], ENT_QUOTES, 'UTF-8') ?>
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
                                    <a href="manage-chapters.php?series_id=<?= (int)$series['id'] ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        Manage Chapters
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

    <div class="mt-3">
        <a href="dashboard.php" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Dashboard
        </a>
    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

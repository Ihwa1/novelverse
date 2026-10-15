<?php $pageTitle = 'My Profile'; require_once __DIR__ . '/includes/header.php'; ?>

<?php if (!$nv_user): ?>
  <div class="container py-5 text-center">
    <h4 class="text-white">You're not logged in 🔒</h4>
    <button class="btn btn-nv mt-3" data-bs-toggle="modal" data-bs-target="#authModal">Login / Register</button>
  </div>
<?php else: ?>

<?php
$pdo = pub_db();
$userId = $nv_user['id'];

// --- 📖 Continue Reading: latest chapter per series ---
$prog = $pdo->prepare(
    'SELECT rp.series_id, rp.chapter_id, rp.updated_at,
            s.title, s.slug, s.cover_url, s.type,
            c.chapter_number, c.title AS chapter_title
     FROM reading_progress rp
     JOIN series s   ON s.id = rp.series_id
     JOIN chapters c ON c.id = rp.chapter_id
     WHERE rp.user_id = ?
     ORDER BY rp.updated_at DESC
     LIMIT 10'
);
$prog->execute([$userId]);
$progress = $prog->fetchAll();

// --- ✍️ Author stats (only if role = author) ---
$isAuthor = ($nv_user['role'] ?? 'reader') === 'author';
$mySeries = [];
$stats = ['series' => 0, 'chapters' => 0, 'views' => 0];

if ($isAuthor) {
    $st = $pdo->prepare('SELECT COUNT(*) FROM series WHERE created_by = ?');
    $st->execute([$userId]);
    $stats['series'] = (int)$st->fetchColumn();

    $st = $pdo->prepare(
        'SELECT COUNT(*) FROM chapters c
         JOIN series s ON s.id = c.series_id
         WHERE s.created_by = ?'
    );
    $st->execute([$userId]);
    $stats['chapters'] = (int)$st->fetchColumn();

    $st = $pdo->prepare(
        'SELECT COALESCE(SUM(c.views), 0) FROM chapters c
         JOIN series s ON s.id = c.series_id
         WHERE s.created_by = ?'
    );
    $st->execute([$userId]);
    $stats['views'] = (int)$st->fetchColumn();

    $st = $pdo->prepare('SELECT * FROM series WHERE created_by = ? ORDER BY id DESC');
    $st->execute([$userId]);
    $mySeries = $st->fetchAll();
}
?>

<main class="container py-4" style="max-width: 1000px;">

  <!-- ===== Header ===== -->
  <div class="d-flex align-items-center mb-4">
    <div style="width:64px;height:64px;border-radius:50%;background:#7c6cf0;display:flex;align-items:center;justify-content:center;font-size:28px;">
      <?= strtoupper(substr($nv_user['username'], 0, 1)) ?>
    </div>
    <div class="ms-3">
      <h3 class="text-white mb-0"><?= htmlspecialchars($nv_user['username']) ?></h3>
      <span class="badge" style="background:<?= $isAuthor ? '#7b5cbf' : '#2a9d8f' ?>;">
        <?= $isAuthor ? '✍️ Author' : '📖 Reader' ?>
      </span>
    </div>
  </div>

  <!-- ===== ✍️ Author stats ===== -->
  <?php if ($isAuthor): ?>
  <div class="row g-3 mb-4">
    <div class="col-4">
      <div class="nv-card text-center py-3">
        <h3 class="text-white mb-0"><?= $stats['series'] ?></h3>
        <small class="text-white-50">Series Published</small>
      </div>
    </div>
    <div class="col-4">
      <div class="nv-card text-center py-3">
        <h3 class="text-white mb-0"><?= $stats['chapters'] ?></h3>
        <small class="text-white-50">Chapters Uploaded</small>
      </div>
    </div>
    <div class="col-4">
      <div class="nv-card text-center py-3">
        <h3 class="text-white mb-0"><?= number_format($stats['views']) ?></h3>
        <small class="text-white-50">Total Views</small>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ===== 📖 Continue Reading ===== -->
  <h5 class="text-white mb-3">📖 Continue Reading</h5>
  <?php if (!$progress): ?>
    <p class="text-white-50 mb-4">Nothing yet — open any chapter and it'll show up here!</p>
  <?php else: ?>
    <div class="row g-3 mb-4">
      <?php foreach ($progress as $p): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <a class="nv-card h-100 d-block" href="reader.php?chapterId=<?= $p['chapter_id'] ?>">
            <div class="card-cover">
              <img src="<?= htmlspecialchars($p['cover_url'] ?: 'https://via.placeholder.com/300x400/0a1128/5b9bff?text=No+Cover') ?>" alt="">
            </div>
            <div class="card-body">
              <h3><?= htmlspecialchars($p['title']) ?></h3>
              <p>Ch. <?= htmlspecialchars($p['chapter_number']) ?><?= $p['chapter_title'] ? ' — ' . htmlspecialchars($p['chapter_title']) : '' ?></p>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- ===== ✍️ My Series (authors only) ===== -->
  <?php if ($isAuthor): ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-white mb-0">✍️ My Series</h5>
    <a href="upload.php" class="btn btn-nv btn-sm">➕ New Series</a>
  </div>
  <?php if (!$mySeries): ?>
    <p class="text-white-50">You haven't published any series yet.</p>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($mySeries as $s): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <a class="nv-card h-100 d-block" href="series.php?slug=<?= htmlspecialchars($s['slug']) ?>">
            <div class="card-cover">
              <img src="<?= htmlspecialchars($s['cover_url'] ?: 'https://via.placeholder.com/300x400/0a1128/5b9bff?text=No+Cover') ?>" alt="">
            </div>
            <div class="card-body">
              <h3><?= htmlspecialchars($s['title']) ?></h3>
              <p><?= htmlspecialchars($s['status'] ?? '') ?><?= $s['is_novel'] ? ' • ₱' . $s['novel_price'] : '' ?></p>
            </div>
          </a>
          <a href="manage.php?slug=<?= htmlspecialchars($s['slug']) ?>" class="btn btn-outline-nv btn-sm w-100 mt-2">📚 Add Chapters</a>
          <a href="edit_series.php?slug=<?= htmlspecialchars($s['slug']) ?>" class="btn btn-outline-nv btn-sm w-100 mt-2">✏️ Edit Series</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php endif; ?>

</main>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
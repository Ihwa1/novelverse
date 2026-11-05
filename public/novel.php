<?php $pageTitle = 'Manage Series'; require_once __DIR__ . '/includes/header.php'; ?>
<?php if (!$nv_user || (($nv_user['role'] ?? '') !== 'author' && (int)($nv_user['is_admin'] ?? 0) !== 1)): ?>
  <div class="container py-5 text-center"><h4 class="text-white">✍️ Authors only</h4></div>
<?php else: ?>

<?php
$pdo = pub_db();
$slug = $_GET['slug'] ?? '';
$st = $pdo->prepare('SELECT * FROM series WHERE slug = ?');
$st->execute([$slug]);
$series = $st->fetch();
if (!$series || ((int)$series['created_by'] !== (int)$nv_user['id'] && (int)($nv_user['is_admin'] ?? 0) !== 1)) {
    echo '<div class="container py-5 text-center"><h4 class="text-white">Series not found or not yours 🔒</h4></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
$ch = $pdo->prepare('SELECT id, chapter_number, title, is_free, views FROM chapters WHERE series_id = ? ORDER BY CAST(chapter_number AS UNSIGNED) ASC');
$ch->execute([$series['id']]);
$chapters = $ch->fetchAll();
$nextNum = 1;
foreach ($chapters as $c) $nextNum = max($nextNum, (int)$c['chapter_number'] + 1);
$isNovel = (int)$series['is_novel'] === 1;
?>

<main class="container py-4" style="max-width: 800px;">
  <h3 class="text-white mb-1"><?= htmlspecialchars($series['title']) ?></h3>
  <p class="text-white-50 mb-2"><?= $isNovel ? '📖 Light Novel' : '🖼️ Comic' ?> • <?= count($chapters) ?> chapters • first <?= (int)$series['free_chapters'] ?> free</p>

  <!-- 🔄 NEW: Back to dashboard button -->
  <a href="profile.php" class="btn btn-outline-light btn-sm mb-4">← Back to My Series</a>

  <!-- Existing chapters -->
  <h5 class="text-white mb-3">📚 Published Chapters</h5>
  <?php if (!$chapters): ?>
    <p class="text-white-50 mb-4">No chapters yet — add the first one below!</p>
  <?php else: ?>
    <div class="nv-card p-3 mb-4">
      <?php foreach ($chapters as $ch): ?>
  <div class="d-flex justify-content-between align-items-center border-bottom py-2">
    <span>Ch. <?= (int)$ch['chapter_number'] ?> <?= htmlspecialchars($ch['title'] ?? '') ?></span>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-<?= $ch['is_free'] ? 'success' : 'warning' ?>"><?= $ch['is_free'] ? 'free' : 'paid' ?></span>
      <button class="btn btn-outline-light btn-sm py-0 toggle-ch"
              data-id="<?= (int)$ch['id'] ?>" data-free="<?= (int)$ch['is_free'] ?>"
              title="Toggle free/paid">🔄</button>
      <button class="btn btn-outline-danger btn-sm py-0 delete-ch"
              data-id="<?= (int)$ch['id'] ?>" title="Delete chapter">🗑️</button>
    </div>
  </div>
<?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Add chapter form -->
  <h5 class="text-white mb-3">➕ Add a Chapter</h5>
  <form id="chapterForm" class="nv-card p-4">
    <input type="hidden" name="series_id" value="<?= $series['id'] ?>">
    <div class="row">
      <div class="col-4 mb-3">
        <label class="form-label text-white">Chapter # *</label>
        <input name="chapter_number" class="form-control" value="<?= $nextNum ?>" required>
      </div>
      <div class="col-8 mb-3">
        <label class="form-label text-white">Chapter title</label>
        <input name="title" class="form-control" placeholder="Optional">
      </div>
    </div>

    <?php if ($isNovel): ?>
      <div class="mb-3">
        <label class="form-label text-white">Chapter text *</label>
        <textarea name="content" class="form-control" rows="12" placeholder="Write or paste the chapter text here..."></textarea>
      </div>
    <?php else: ?>
      <!-- 🔄 NEW: MangaDex import option (comics only) -->
      <div class="mb-3">
        <label class="form-label text-white">🔄 OR MangaDex Chapter ID <span class="text-white-50">(leave empty for local upload)</span></label>
        <input name="md_chapter_id" class="form-control" placeholder="e.g. f4fa82a7-4c09-4906-bcf3-60fda30bae54">
        <small class="text-white-50">Grab it from the chapter's URL on MangaDex. If filled, page files below are ignored.</small>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">Page images (select multiple, in order)</label>
        <input name="pages[]" type="file" accept="image/*" multiple class="form-control">
        <small class="text-white-50">Pages appear in the order you select them</small>
      </div>
    <?php endif; ?>

    <div id="err" class="text-danger mb-2" style="display:none;"></div>
    <button class="btn btn-nv w-100">📤 Upload Chapter</button>
  </form>
</main>

<script src="js/app.js"></script>
<script>
  const API_BASE = '../backend/api/';   // ⚠️ same as upload.php — match your api() base

  document.getElementById('chapterForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const err = document.getElementById('err');
    err.style.display = 'none';
    try {
      const fd = new FormData(e.target);
      // 🔄 ensure md_chapter_id is always sent (empty string = local upload mode)
      if (!fd.has('md_chapter_id')) fd.append('md_chapter_id', '');
      const r = await fetch(API_BASE + 'upload.php?action=chapter', { method: 'POST', body: fd });
      const res = await r.json();
      if (!r.ok) throw new Error(res.error || 'Upload failed');
      location.reload();   // refresh to show the new chapter
    } catch (ex) {
      err.textContent = ex.message;
      err.style.display = '';
    }
  });
</script>
<script>
document.querySelectorAll('.toggle-ch').forEach(btn => {
  btn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('chapter_id', btn.dataset.id);
    fd.append('action', 'toggle');
    const r = await fetch('../backend/api/chapter_actions.php', { method: 'POST', body: fd });
    const res = await r.json();
    if (res.ok) location.reload();          // 🔄 refresh to show new free/paid state
    else alert(res.error || 'Toggle failed');
  });
});

document.querySelectorAll('.delete-ch').forEach(btn => {
  btn.addEventListener('click', async () => {
    if (!confirm('Delete this chapter for real? 🗑️')) return;
    const fd = new FormData();
    fd.append('chapter_id', btn.dataset.id);
    fd.append('action', 'delete');
    const r = await fetch('../backend/api/chapter_actions.php', { method: 'POST', body: fd });
    const res = await r.json();
    if (res.ok) location.reload();
    else alert(res.error || 'Delete failed');
  });
});
</script>

<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
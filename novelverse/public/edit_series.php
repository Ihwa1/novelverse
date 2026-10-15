<?php $pageTitle = 'Edit Series'; require_once __DIR__ . '/includes/header.php'; ?>
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
?>

<main class="container py-4" style="max-width: 600px;">
  <h3 class="text-white mb-1">✏️ Edit Series</h3>
  <p class="text-white-50 mb-4"><?= htmlspecialchars($series['title']) ?></p>

  <a href="manage.php?slug=<?= urlencode($series['slug']) ?>" class="btn btn-outline-light btn-sm mb-4">← Back to Manage</a>

  <form id="editForm" class="nv-card p-4">
    <input type="hidden" name="series_id" value="<?= $series['id'] ?>">

    <div class="mb-3">
      <label class="form-label text-white">Title *</label>
      <input name="title" class="form-control" value="<?= htmlspecialchars($series['title']) ?>" required>
    </div>

    <div class="row">
      <div class="col-6 mb-3">
        <label class="form-label text-white">Type *</label>
        <select name="type" class="form-control">
          <?php foreach (['Manga','Manhwa','Manhua','Light Novel'] as $t): ?>
            <option value="<?= $t ?>" <?= ($series['type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 mb-3">
        <label class="form-label text-white">Status</label>
        <select name="status" class="form-control">
          <?php foreach (['ongoing','completed','hiatus'] as $s): ?>
            <option value="<?= $s ?>" <?= ($series['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label text-white">Author name</label>
      <input name="author_name" class="form-control" value="<?= htmlspecialchars($series['author_name'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label text-white">Description</label>
      <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($series['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label text-white">Free chapters</label>
      <input name="free_chapters" type="number" min="0" class="form-control" value="<?= (int)$series['free_chapters'] ?>">
      <small class="text-white-50">Chapters after this are 🔒 paid</small>
    </div>

    <div class="mb-3">
      <label class="form-label text-white">Cover image <span class="text-white-50">(leave empty to keep current)</span></label>
      <input name="cover" type="file" accept="image/*" class="form-control">
    </div>

    <div id="err" class="text-danger mb-2" style="display:none; white-space: pre-wrap; word-break: break-all;"></div>
    <button class="btn btn-nv w-100">💾 Save Changes</button>
  </form>
</main>

<script src="js/app.js"></script>
<script>
  const API_BASE = '../backend/api/';

  document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const err = document.getElementById('err');
    err.style.display = 'none';
    try {
      const fd = new FormData(e.target);
      const r = await fetch(API_BASE + 'update_series.php', { method: 'POST', body: fd });

      // 🔍 DEBUG: grab raw text first so PHP errors don't get swallowed
      const raw = await r.text();
      console.log('RAW RESPONSE:', raw);

      let res;
      try {
        res = JSON.parse(raw);
      } catch (parseErr) {
        // 🚨 backend printed HTML/PHP errors instead of JSON — show them
        err.textContent = 'Backend did not return JSON. Raw response:\n' + raw;
        err.style.display = '';
        return;
      }

      if (!r.ok) throw new Error(res.error || 'Update failed');
      // ✅ saved — go back to manage page for this series
      window.location.href = 'manage.php?slug=<?= urlencode($series['slug']) ?>';
    } catch (ex) {
      err.textContent = ex.message;
      err.style.display = '';
    }
  });
</script>

<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
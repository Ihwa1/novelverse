<?php $pageTitle = 'Publish a Series'; require_once __DIR__ . '/includes/header.php'; ?>
<?php if (!$nv_user || (($nv_user['role'] ?? '') !== 'author' && (int)($nv_user['is_admin'] ?? 0) !== 1)): ?>
  <div class="container py-5 text-center">
    <h4 class="text-white">✍️ Authors only</h4>
    <p class="text-white-50">You need an author account to publish series.</p>
  </div>
<?php else: ?>

<main class="container py-4" style="max-width: 640px;">
  <h3 class="text-white mb-4">✍️ Publish a New Series</h3>
  <a href="index.php" class="btn btn-outline-light btn-sm mb-4">← Back to My Series</a>

  <form id="uploadForm" class="nv-card p-4">
    <div class="mb-3">
      <label class="form-label text-white">Title *</label>
      <input name="title" class="form-control" required>
    </div>
    <div class="row">
      <div class="col-6 mb-3">
        <label class="form-label text-white">Type *</label>
        <select name="type" id="typeSelect" class="form-select" required>
          <option value="manhwa">Manhwa</option>
          <option value="manhua">Manhua</option>
          <option value="manga">Manga</option>
          <option value="novel">Light Novel</option>
        </select>
      </div>
      <div class="col-6 mb-3">
        <label class="form-label text-white">Status</label>
        <select name="status" class="form-select">
          <option value="ongoing">Ongoing</option>
          <option value="completed">Completed</option>
          <option value="hiatus">Hiatus</option>
        </select>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label text-white">Author name</label>
      <input name="author" class="form-control" placeholder="Defaults to your username">
    </div>
    <div class="mb-3">
      <label class="form-label text-white">Description</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <div class="row">
      <div class="col-6 mb-3">
        <label class="form-label text-white">Free chapters</label>
        <input name="free_chapters" type="number" min="0" value="3" class="form-control">
        <small class="text-white-50">Chapters after this are 🔒 paid</small>
      </div>
      <div class="col-6 mb-3" id="priceWrap" style="display:none;">
        <label class="form-label text-white">Price (₱) *</label>
        <input name="novel_price" type="number" step="0.01" min="0" class="form-control" placeholder="e.g. 150.00">
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label text-white">Cover image</label>
      <input name="cover" type="file" accept="image/*" class="form-control">
    </div>
    <div id="err" class="text-danger mb-2" style="display:none;"></div>
    <button class="btn btn-nv w-100">🚀 Create Series</button>
  </form>
</main>

<script src="js/app.js"></script>
<script>
  // ⚠️ Set this to the same base path your api() helper uses (check js/app.js)
  const API_BASE = '../backend/api/';

  const typeSel = document.getElementById('typeSelect');
  typeSel.addEventListener('change', () => {
    document.getElementById('priceWrap').style.display = typeSel.value === 'novel' ? '' : 'none';
  });

  document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const err = document.getElementById('err');
    err.style.display = 'none';
    const fd = new FormData(e.target);
    try {
      const r = await fetch(API_BASE + 'upload.php?action=series', { method: 'POST', body: fd });
      const res = await r.json();
      if (!r.ok) throw new Error(res.error || 'Upload failed');
      location = 'manage.php?slug=' + res.data.slug;   // ➡️ go add chapters!
    } catch (ex) {
      err.textContent = ex.message;
      err.style.display = '';
    }
  });
</script>

<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
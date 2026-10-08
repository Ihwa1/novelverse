<?php $pageTitle = 'Admin'; require_once __DIR__ . '/../includes/header.php'; ?>
<main class="container py-4" style="max-width: 900px;">
  <h2 class="text-white mb-4">🛠️ Admin Panel</h2>
  <div id="msg" class="alert d-none"></div>

  <div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
      <h5 class="text-white">➕ Add Series</h5>
      <form id="seriesForm" class="row g-2">
        <div class="col-md-6"><input name="title" class="form-control" placeholder="Title" required></div>
        <div class="col-md-3">
          <select name="type" class="form-select">
            <option value="manga">Manga</option><option value="manhwa">Manhwa</option>
            <option value="manhua">Manhua</option><option value="novel">Novel</option>
          </select>
        </div>
        <div class="col-md-3"><input name="status" class="form-control" placeholder="Status (ongoing)" value="ongoing"></div>
        <div class="col-12"><input name="cover_url" class="form-control" placeholder="Cover URL"></div>
        <div class="col-12"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
        <div class="col-auto"><button class="btn btn-primary">Save Series</button></div>
      </form>
    </div>
  </div>

  <div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
      <h5 class="text-white">📄 Add Chapter</h5>
      <form id="chapterForm" class="row g-2">
        <div class="col-md-6"><select name="series_id" id="seriesSelect" class="form-select" required></select></div>
        <div class="col-md-2"><input name="chapter_number" type="number" step="0.1" class="form-control" placeholder="Ch #" value="1"></div>
        <div class="col-md-4"><input name="title" class="form-control" placeholder="Chapter title"></div>
        <div class="col-12">
          <textarea name="pages" class="form-control" rows="5" placeholder="Page image URLs — one per line"></textarea>
        </div>
        <div class="col-12"><textarea name="content" class="form-control" rows="3" placeholder="Text content (novels only)"></textarea></div>
        <div class="col-auto form-check align-self-center ms-2">
          <input class="form-check-input" type="checkbox" name="is_free" id="isFree" checked>
          <label class="form-check-label text-white" for="isFree">Free chapter</label>
        </div>
        <div class="col-auto"><button class="btn btn-primary">Save Chapter</button></div>
      </form>
    </div>
  </div>

  <h5 class="text-white mb-2">📚 Your Series</h5>
  <div id="seriesList"></div>
</main>

<script src="../js/app.js"></script>
<script>
  const show = (m, ok = true) => {
    const el = document.getElementById('msg');
    el.className = `alert alert-${ok ? 'success' : 'danger'}`;
    el.textContent = m;
  };

  async function loadSeries() {
    const res = await api('admin_series.php');
    const list = res.data;
    document.getElementById('seriesSelect').innerHTML =
      list.map(s => `<option value="${s.id}">${s.title}</option>`).join('');
    document.getElementById('seriesList').innerHTML = list.length
      ? list.map(s => `<div class="d-flex justify-content-between border-bottom py-2">
            <span class="text-white">${s.title} <small class="text-muted-nv">(${s.type})</small></span>
            <a class="btn btn-sm btn-outline-light" href="series.php?slug=${s.slug}">View</a>
          </div>`).join('')
      : '<p class="text-muted-nv">Nothing yet.</p>';
  }

  document.getElementById('seriesForm').addEventListener('submit', async e => {
    e.preventDefault();
    const f = Object.fromEntries(new FormData(e.target));
    try { const r = await api('admin_series.php', { method: 'POST', body: JSON.stringify(f) });
      show(`Series created! (id ${r.data.id})`); e.target.reset(); loadSeries();
    } catch (err) { show(err.message, false); }
  });

  document.getElementById('chapterForm').addEventListener('submit', async e => {
    e.preventDefault();
    const f = Object.fromEntries(new FormData(e.target));
    f.is_free = document.getElementById('isFree').checked;
    try { const r = await api('admin_chapters.php', { method: 'POST', body: JSON.stringify(f) });
      show(`Chapter saved with ${r.data.pages} pages! 🎉`); e.target.reset();
    } catch (err) { show(err.message, false); }
  });

  loadSeries().catch(err => show(err.message + ' — are you logged in as an admin?', false));
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
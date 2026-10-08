<?php $pageTitle = 'Browse'; require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-4">
  <h4 class="section-title mb-4" id="pageTitle">Browse</h4>

  <div class="input-group mb-4" style="max-width:600px">
    <input class="form-control" id="mdSearch" placeholder="Search MangaDex to import...">
    <button class="btn btn-nv" onclick="searchMangaDex()">
      <i class="bi bi-cloud-download me-1"></i>Search MangaDex
    </button>
  </div>

  <div id="grid" class="row g-4"></div>
</main>

<div class="modal fade" id="importModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-header border-0">
        <h5 class="modal-title text-white">Import Series</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="impInfo" class="text-muted-nv"></p>
        <label class="form-label">Type</label>
        <select class="form-select mb-3" id="impType">
          <option value="manga">Manga</option>
          <option value="manhwa">Manhwa</option>
          <option value="manhua">Manhua</option>
        </select>
        <button class="btn btn-nv w-100" id="impBtn">Import to NovelVerse</button>
      </div>
    </div>
  </div>
</div>

<script src="js/app.js"></script>
<script>
  const params = new URLSearchParams(location.search);
  const type = params.get('type'), q = params.get('q');
  let pendingImport = null;

  const titles = { manga: 'Manga', manhwa: 'Manhwa', manhua: 'Manhua', novel: 'Light Novels' };
  if (titles[type]) document.getElementById('pageTitle').textContent = titles[type];

  function cardHTML(s, importable = false) {
    const badge = TYPE_BADGE[s.type] || TYPE_BADGE.manga;
    return `
    <div class="col-6 col-md-4 col-lg-3">
      <div class="nv-card h-100">
        <div class="card-cover">
          <img src="${s.cover_url || s.cover || 'https://via.placeholder.com/300x400/0a1128/5b9bff?text=No+Cover'}" loading="lazy" alt="">
          <span class="type-badge ${s.type}">${badge.label}</span>
        </div>
        <div class="card-body">
          <h3>${s.title}</h3>
          <p>${s.status || ''}${s.is_novel ? ' • ₱' + s.novel_price : ''}</p>
          ${importable
            ? `<button class="btn btn-outline-nv btn-sm w-100 mt-2"
                 onclick='openImport(${JSON.stringify(JSON.stringify(s))})'>
                 <i class="bi bi-plus-lg me-1"></i>Import</button>`
            : `<a class="btn btn-nv btn-sm w-100 mt-2" href="${s.type === 'novel' ? 'novel' : 'series'}.php?slug=${s.slug}">
                 <i class="bi bi-book me-1"></i>Read</a>`}
        </div>
      </div>
    </div>`;
  }

  async function loadLocal() {
    const res = await api('series.php' + (type ? `?type=${type}` : ''));
    document.getElementById('grid').innerHTML = res.data.map(s => cardHTML(s)).join('');
  }
  loadLocal();

  async function searchMangaDex() {
    const query = document.getElementById('mdSearch').value || q || '';
    if (!query) return;
    document.getElementById('grid').innerHTML =
      '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';
    const res = await api(`mangadex.php?action=search&q=${encodeURIComponent(query)}`);
    document.getElementById('grid').innerHTML =
      res.data.map(m => cardHTML({ ...m, cover_url: m.cover }, true)).join('') ||
      '<p class="text-muted-nv">No results found.</p>';
  }
  if (q) { document.getElementById('mdSearch').value = q; searchMangaDex(); }

  function openImport(jsonStr) {
    pendingImport = JSON.parse(jsonStr);
    document.getElementById('impInfo').innerHTML =
      `<b class="text-white">${pendingImport.title}</b><br>
       <img src="${pendingImport.cover}" class="rounded my-2" width="90">`;
    document.getElementById('impType').value = pendingImport.type;
    new bootstrap.Modal('#importModal').show();
  }

  document.getElementById('impBtn').onclick = async () => {
    try {
      const res = await api('series.php', {
        method: 'POST',
        body: JSON.stringify({
          title: pendingImport.title, type: impType.value,
          description: pendingImport.desc, cover: pendingImport.cover,
          status: pendingImport.status,
          external_source: 'mangadex', external_id: pendingImport.id
        })
      });

      // Server says it already exists → friendly message, no crash
      if (res && res.status === 'exists') {
        alert('Already in NovelVerse! 🔄');
      } else {
        alert('Imported to NovelVerse! 🎉');
      }

      bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
      loadLocal();
    } catch (e) {
      // Fallback: if the API throws a raw duplicate error, catch it here too
      if (e.message && e.message.toLowerCase().includes('duplicate')) {
        alert('Already in NovelVerse! 🔄');
        bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
        loadLocal();
      } else {
        alert(e.message);
      }
    }
  };
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
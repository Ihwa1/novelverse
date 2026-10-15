<?php $pageTitle = 'Browse'; require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-4">
  <h4 class="section-title mb-4" id="pageTitle">Browse</h4>

  <div class="input-group mb-4" style="max-width:720px">
    <input class="form-control" id="mdSearch" placeholder="Search to import...">
    <select class="form-select" id="searchSource" style="max-width:180px">
      <option value="mangadex">MangaDex</option>
      <option value="manhwa">Manhwa/Manhua</option>
      <option value="novel">Public Domain Novel</option>
    </select>
    <button class="btn btn-nv" onclick="searchExternal()">
      <i class="bi bi-cloud-download me-1"></i>Search
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
          <option value="novel">Light Novel</option>
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
  let searchResults = [];

  const titles = { manga: 'Manga', manhwa: 'Manhwa', manhua: 'Manhua', novel: 'Light Novels' };
  if (titles[type]) document.getElementById('pageTitle').textContent = titles[type];

  function cardHTML(s, importable = false, index = null) {
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
            ? `<button class="btn btn-outline-nv btn-sm w-100 mt-2" onclick="openImport(${index})">
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

  async function searchExternal() {
    const query  = document.getElementById('mdSearch').value || q || '';
    const source = document.getElementById('searchSource').value;
    if (!query) return;

    document.getElementById('grid').innerHTML =
      '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';

    let endpoint = 'mangadex.php?action=search';
    if (source === 'manhwa') endpoint = 'manhwa.php?action=search';
    if (source === 'novel')  endpoint = 'novels.php?action=search';

    try {
      const res = await api(`${endpoint}&q=${encodeURIComponent(query)}`);
      searchResults = (res.data || []).map(m => ({ ...m, cover_url: m.cover, _source: source }));
      document.getElementById('grid').innerHTML =
        searchResults.map((m, i) => cardHTML(m, true, i)).join('') ||
        '<p class="text-muted-nv">No results found.</p>';
    } catch (e) {
      document.getElementById('grid').innerHTML =
        '<p class="text-muted-nv">Search failed. The external source may be down — try another source.</p>';
    }
  }
  if (q) { document.getElementById('mdSearch').value = q; searchExternal(); }

  function openImport(index) {
    pendingImport = searchResults[index];
    if (!pendingImport) return alert('Import data missing');

    document.getElementById('impInfo').innerHTML =
      `<b class="text-white">${pendingImport.title}</b><br>
       <img src="${pendingImport.cover || ''}" class="rounded my-2" width="90">`;
    document.getElementById('impType').value = pendingImport.type || 'novel';
    new bootstrap.Modal('#importModal').show();
  }

  document.getElementById('impBtn').onclick = async () => {
    try {
      const source = pendingImport._source || document.getElementById('searchSource').value || 'mangadex';
      const res = await api('series.php', {
        method: 'POST',
        body: JSON.stringify({
          title: pendingImport.title,
          type: document.getElementById('impType').value,
          description: pendingImport.desc || '',
          cover: pendingImport.cover || null,
          status: pendingImport.status || 'ongoing',
          author: pendingImport.author || '',
          external_source: source,
          external_id: pendingImport.id
        })
      });

      if (res && res.status === 'exists') {
        alert('Already in NovelVerse!');
      } else {
        alert('Imported to NovelVerse!');
      }

      bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
      loadLocal();
    } catch (e) {
      if (e.message && e.message.toLowerCase().includes('duplicate')) {
        alert('Already in NovelVerse!');
        bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
        loadLocal();
      } else {
        alert(e.message || 'Import failed');
      }
    }
  };
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
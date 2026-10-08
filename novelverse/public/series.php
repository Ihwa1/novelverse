<?php $pageTitle = 'Series'; require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-4" id="detail">
  <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
</main>

<script src="js/app.js"></script>
<script>
  const slug = new URLSearchParams(location.search).get('slug');

  (async () => {
  const res = await api(`series.php?slug=${slug}`);
  const s = res.data;
  const badge = TYPE_BADGE[s.type] || TYPE_BADGE.manga;

  // Prefer LOCAL chapters (from your DB) — fall back to MangaDex only if none exist
  const localChapters = s.chapters || [];
  let mdChapters = [];
  if (!localChapters.length && s.external_source === 'mangadex' && s.external_id) {
    const md = await api(`mangadex.php?action=chapters&id=${s.external_id}`);
    mdChapters = md.data;
  }

  const chapterList = localChapters.length
    ? localChapters.map(c => `
        <a class="chapter-item" href="reader.php?chapterId=${c.id}">
          <span><i class="bi bi-images me-2 text-primary"></i>
            Ch. ${c.chapter_number ?? '?'} — ${c.title || ''}</span>
          <span class="tag free">Local</span>
        </a>`).join('')
    : mdChapters.map(c => `
        <a class="chapter-item" href="reader.php?chapterId=${c.id}">
          <span><i class="bi bi-images me-2 text-primary"></i>
            Ch. ${c.chapter ?? '?'} — ${c.title || ''}</span>
          <span class="tag free">${c.pages} pages</span>
        </a>`).join('');

  document.getElementById('detail').innerHTML = `
    <div class="row g-4 mb-4">
      <div class="col-md-3 col-5">
        <img src="${s.cover_url}" class="img-fluid rounded shadow">
      </div>
      <div class="col-md-9 col-7">
        <span class="type-badge ${s.type} inline">${badge.label}</span>
        <h1 class="text-white mt-2">${s.title}</h1>
        <p class="text-muted-nv">${s.description || ''}</p>
        <span class="tag owned">${s.status}</span>
      </div>
    </div>
    <h4 class="section-title mb-3">Chapters</h4>
    ${chapterList || '<p class="text-muted-nv">No chapters yet.</p>'}`;
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
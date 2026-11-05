<?php $pageTitle = 'Reader'; require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-3">
  <div id="reader" class="reader-container">
    <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
  </div>
</main>

<div id="readerNav" class="reader-nav">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a id="navPrev" href="#" class="btn btn-outline-light btn-sm">‹ Prev</a>
    <select id="chapterJump" class="form-select form-select-sm mx-2" style="max-width: 260px;"></select>
    <a id="navNext" href="#" class="btn btn-outline-light btn-sm">Next ›</a>
  </div>
</div>

<script src="/public/js/app.js?v=105"></script>
<script>
  const chapterId = new URLSearchParams(location.search).get('chapterId');
  const title = new URLSearchParams(location.search).get('title');

  (async () => {
    const box = document.getElementById('reader');
    if (!chapterId) {
      box.innerHTML = '<p class="text-muted-nv text-center py-5">No chapter specified.</p>';
      return;
    }

    try {
      let pages = [];
      let textContent = null;
      let chapterData = null;

      const isExternal = chapterId.startsWith('comick:') || chapterId.startsWith('mk:') || chapterId.includes('-');

      if (isExternal) {
        const md = await api(`mangadex.php?action=pages&chapterId=${encodeURIComponent(chapterId)}${title ? '&title=' + encodeURIComponent(title) : ''}`);
        pages = md.data?.pages || [];
      } else {
        try {
          const res = await api(`chapters.php?id=${encodeURIComponent(chapterId)}`);
          chapterData = res.data || {};
          pages = chapterData.pages || [];
          textContent = chapterData.content || null;

          if ((!pages || !pages.length) && !textContent && chapterData.external_id) {
            const md = await api(`mangadex.php?action=pages&chapterId=${encodeURIComponent(chapterData.external_id)}${title ? '&title=' + encodeURIComponent(title) : ''}`);
            pages = md.data?.pages || [];
          }
        } catch (err) {
          const md = await api(`mangadex.php?action=pages&chapterId=${encodeURIComponent(chapterId)}${title ? '&title=' + encodeURIComponent(title) : ''}`);
          pages = md.data?.pages || [];
        }
      }

      if (pages && pages.length) {
        box.innerHTML = pages.map(p =>
          `<img src="${p}" loading="lazy" alt=""
                referrerpolicy="no-referrer"
                draggable="false"
                onerror="this.onerror=null; console.error('Failed image:', this.src);">`
        ).join('');
        return;
      }

      box.innerHTML = `<p class="text-muted-nv text-center py-5">No pages found for chapter (${chapterId}).</p>`;
    } catch (e) {
      box.innerHTML = `<div class="alert alert-danger text-center my-4">
        <h5>Reader Error</h5>
        <p>${e.message || 'Failed to load chapter pages.'}</p>
      </div>`;
    }
  })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
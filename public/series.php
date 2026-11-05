<?php $pageTitle = 'Series'; require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-4" id="detail">
  <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
</main>

<script src="/public/js/app.js?v=103"></script>
<script>
  const slug = new URLSearchParams(location.search).get('slug');
  const PH = (typeof PLACEHOLDER !== 'undefined')
    ? PLACEHOLDER
    : 'https://via.placeholder.com/300x400/0a1128/5b9bff?text=No+Cover';

  function chapterTag(c) {
    const price = Number(c.coin_price || 0);
    const isFree = c.is_free == 1 || c.is_free === true || price <= 0;
    const unlocked = c.unlocked == 1 || c.unlocked === true;

    if (isFree) {
      return '<span class="tag free">Free</span>';
    }
    if (unlocked) {
      return '<span class="tag owned">Owned</span>';
    }
    return `<span class="tag locked"><i class="bi bi-lock-fill me-1"></i>${price} coins</span>`;
  }

  (async () => {
    try {
      const res = await api(`series.php?slug=${encodeURIComponent(slug)}`);
      const s = res.data;
      if (!s) throw new Error('Series not found');

      const badge = (typeof TYPE_BADGE !== 'undefined' && TYPE_BADGE[s.type])
        ? TYPE_BADGE[s.type]
        : { label: (s.type || 'manga').toUpperCase() };

      const localChapters = s.chapters || [];
      let mdChapters = [];

      if (!localChapters.length && (s.external_source === 'mangadex' || s.external_id || s.mangadex_id)) {
        const extId = s.external_id || s.mangadex_id || s.id;
        const md = await api(`mangadex.php?action=chapters&id=${encodeURIComponent(extId)}&title=${encodeURIComponent(s.title || '')}`);
        mdChapters = md.data || [];
      }

      const chapterList = localChapters.length
        ? localChapters.map(c => `
            <a class="chapter-item" href="reader.php?chapterId=${c.id}&title=${encodeURIComponent(s.title || '')}">
              <span>
                <i class="bi bi-images me-2 text-primary"></i>
                Ch. ${c.chapter_number ?? '?'} ${c.title ? '— ' + c.title : ''}
              </span>
              ${chapterTag(c)}
            </a>
          `).join('')
        : mdChapters.map(c => {
            const pageBadge = (c.pages && c.pages > 0)
              ? `${c.pages} pages`
              : 'Available';

            return `
              <a class="chapter-item" href="reader.php?chapterId=${encodeURIComponent(c.id)}&title=${encodeURIComponent(s.title || '')}">
                <span>
                  <i class="bi bi-images me-2 text-primary"></i>
                  Ch. ${c.chapter ?? '?'} ${c.title ? '— ' + c.title : ''}
                  ${c.source === 'comick' ? '<span class="badge bg-secondary ms-2" style="font-size: 0.7em;">ComicK</span>' : ''}
                </span>
                <span class="tag free">${pageBadge}</span>
              </a>
            `;
          }).join('');

      let rawCover = s.cover_url || s.cover || '';
      let cover = (typeof coverUrl === 'function') ? coverUrl(rawCover) : rawCover;
      if (!cover) cover = PH;

      document.getElementById('detail').innerHTML = `
        <div class="row g-4 mb-4">
          <div class="col-md-3 col-5">
            <img
              src="${cover}"
              alt="${s.title}"
              class="img-fluid rounded shadow"
              loading="lazy"
              referrerpolicy="no-referrer"
              onerror="
                if (this.src.includes('.512.jpg')) {
                  this.src = this.src.replace('.512.jpg', '');
                } else if (this.src.includes('uploads.mangadex.org') && !this.src.includes('proxy.php')) {
                  this.src = '/proxy.php?url=' + encodeURIComponent(this.src);
                } else {
                  this.onerror = null;
                  this.src = '${PH}';
                }
              "
            >
          </div>
          <div class="col-md-9 col-7">
            <span class="type-badge ${s.type || 'manga'} inline">${badge.label}</span>
            <h1 class="text-white mt-2">${s.title}</h1>
            <p class="text-muted-nv">${s.description || s.desc || 'No description available.'}</p>
            <span class="tag owned">${s.status || 'Ongoing'}</span>
          </div>
        </div>
        <h4 class="section-title mb-3">Chapters</h4>
        ${chapterList || '<p class="text-muted-nv">No chapters found for this comic.</p>'}
      `;
    } catch (e) {
      document.getElementById('detail').innerHTML =
        `<div class="alert alert-danger">Failed to load series: ${e.message || 'error'}</div>`;
    }
  })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
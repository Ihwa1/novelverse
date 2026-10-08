<?php $pageTitle = 'Reader'; require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-3">
  <div id="reader" class="reader-container">
    <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
  </div>
</main>

<!-- 🧭 Asura-style floating reader nav (hidden until you tap the screen) -->
<div id="readerNav" class="reader-nav">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <a id="navPrev" href="#" class="btn btn-outline-light btn-sm">‹ Prev</a>
    <select id="chapterJump" class="form-select form-select-sm mx-2" style="max-width: 260px;"></select>
    <a id="navNext" href="#" class="btn btn-outline-light btn-sm">Next ›</a>
  </div>
</div>

<script src="js/app.js"></script>
<script>
  const chapterId = new URLSearchParams(location.search).get('chapterId');

  (async () => {
    const box = document.getElementById('reader');

    try {
      const res = await api(`chapters.php?id=${chapterId}`);
      const c = res.data;

      // Pages: local pages_json first, else fetch live from MangaDex via external_id
      let pages = c.pages;
      if ((!pages || !pages.length) && c.external_id) {
        const md = await api(`mangadex.php?action=pages&chapterId=${c.external_id}`);
        pages = md.data.pages;
      }

      if (pages && pages.length) {
        box.innerHTML = pages.map(p => `<img src="${p}" loading="lazy">`).join('');
        // ✅ Progress tracking now works for imported chapters too — they're real DB rows!
        trackProgress(c.series_id, c.id);
        buildNav(c);
        return;
      }

      box.innerHTML = '<p class="text-muted-nv text-center py-5">No pages found for this chapter.</p>';
      buildNav(c);

    } catch (e) {
      // 🔒 Locked chapter → show unlock prompt with your buy flow
      if (e.json && e.json.locked) {
        const sid = e.json.series.id, price = e.json.price;
        box.innerHTML = `
          <div class="text-center py-5">
            <i class="bi bi-lock-fill display-3 text-warning"></i>
            <h4 class="text-white mt-3">${e.json.series.title}</h4>
            <p class="text-muted-nv">${e.json.message}</p>
            <p class="text-white">Price: ₱${price}</p>
            <button class="btn btn-nv me-2" onclick="buyNovel(${sid}, 'gcash')">
              <i class="bi bi-phone me-1"></i>Unlock with GCash
            </button>
            <button class="btn btn-outline-nv" onclick="buyNovel(${sid}, 'card')">
              <i class="bi bi-credit-card me-1"></i>Pay with Card
            </button>
          </div>`;
        return;
      }
      box.innerHTML = '<p class="text-muted-nv text-center py-5">No pages found for this chapter.</p>';
    }
  })();

  // 🧭 Build the floating nav from the API's prev/next/chapters data
  function buildNav(c) {
    const prev = document.getElementById('navPrev');
    const next = document.getElementById('navNext');
    const jump = document.getElementById('chapterJump');

    if (c.prev) prev.href = `reader.php?chapterId=${c.prev.id}`;
    else { prev.classList.add('disabled'); prev.removeAttribute('href'); }

    if (c.next) next.href = `reader.php?chapterId=${c.next.id}`;
    else { next.classList.add('disabled'); next.removeAttribute('href'); }

    jump.innerHTML = (c.chapters || []).map(ch =>
      `<option value="${ch.id}" ${ch.id == chapterId ? 'selected' : ''}>
         Chapter ${ch.chapter_number}${ch.title ? ' — ' + ch.title : ''}
       </option>`).join('');

    jump.addEventListener('change', function () {
      if (this.value) location.href = `reader.php?chapterId=${this.value}`;
    });
  }

  // 👆 Tap-to-show / auto-hide nav (Asura style)
  (() => {
    const nav = document.getElementById('readerNav');
    let hideTimer;

    function showNav() {
      nav.classList.add('show');
      clearTimeout(hideTimer);
      hideTimer = setTimeout(() => nav.classList.remove('show'), 4000);
    }

    document.addEventListener('click', (e) => {
      if (nav.contains(e.target)) return;          // clicking the bar itself = real click
      if (nav.classList.contains('show')) nav.classList.remove('show');
      else showNav();
    });

    showNav(); // flash it on load so readers know it's there
  })();
</script>

<style>
.reader-nav {
  position: fixed;
  left: 0; right: 0; bottom: 0;
  background: rgba(10, 14, 30, 0.95);
  border-top: 1px solid rgba(255,255,255,0.1);
  z-index: 1050;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease;
}
.reader-nav.show { opacity: 1; pointer-events: auto; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
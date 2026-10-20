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

  // 💓 Heartbeat — records the read so trending/popular/Top Readers come alive.
  // Fire-and-forget: failures are silently ignored (guests, migration not run, etc.)
  function trackProgress(seriesId, chId) {
    fetch('api/progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ series_id: seriesId, chapter_id: chId })
    }).catch(() => {});   // never let tracking break the reading experience
  }

  (async () => {
    const box = document.getElementById('reader');

    try {
      const res = await api(`chapters.php?id=${chapterId}`);
      const c = res.data;

      // Pages: local pages_json first, else fetch live from MangaDex via external_id
            // Pages / content priority:
      // 1. Local pages_json
      // 2. Local text content (novels)
      // 3. Live fetch from external source (mangadex / manhwa / novel)
      let pages = c.pages;
      let textContent = c.content || null;

      if ((!pages || !pages.length) && !textContent && c.external_id) {
        const source = c.external_source || 'mangadex';

        if (source === 'manhwa') {
          const mh = await api(`manhwa.php?action=pages&chapterId=${c.external_id}`);
          pages = mh.data?.pages || [];
        } else if (source === 'novel' || source === 'gutendex') {
          const nv = await api(`novels.php?action=content&chapterId=${c.external_id}`);
          textContent = nv.data?.content || null;
        } else {
          // default = MangaDex
          const md = await api(`mangadex.php?action=pages&chapterId=${c.external_id}`);
          pages = md.data?.pages || [];
        }
      }

      if (pages && pages.length) {
        box.innerHTML = pages.map(p => `<img src="${p}" loading="lazy">`).join('');
        trackProgress(c.series_id, c.id);
        buildNav(c);
        return;
      }

      if (textContent) {
  box.innerHTML = `
    <div class="novel-reader-wrap">
      <article class="novel-text">
        ${textContent.replace(/\n/g, '<br>')}
      </article>
    </div>`;
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

.novel-reader-wrap {
  background: #f5f0e8;
  min-height: 70vh;
  padding: 2rem 1rem 5rem;
  border-radius: 12px;
}

.novel-text {
  max-width: 720px;
  margin: 0 auto;
  background: #fffef9;
  color: #1a1a1a;
  padding: 2.5rem 2rem;
  line-height: 1.9;
  font-size: 1.1rem;
  font-family: Georgia, "Times New Roman", serif;
  border-radius: 8px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.12);
  white-space: normal;
  word-wrap: break-word;
}

@media (max-width: 576px) {
  .novel-text {
    padding: 1.5rem 1rem;
    font-size: 1rem;
  }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
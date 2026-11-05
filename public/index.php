<?php $pageTitle = 'Home'; require_once __DIR__ . '/includes/header.php'; ?>

<!-- ============ HERO SLIDESHOW ============ -->
<section class="hero-section mb-4">
  <div id="heroSlideshow" class="hero-slideshow">
    <div class="hero-loading text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>
  </div>
</section>

<!-- ============ MAIN LAYOUT ============ -->
<div class="container">
  <div class="row g-4">
    <div class="col-lg-8">
      <section class="trending-panel mb-4">
        <div class="trending-header">
          <div class="th-left">
            <div class="th-icon">🔥</div>
            <h2>Trending Comics</h2>
          </div>
          <a href="browse.php" class="th-all btn btn-sm btn-nv">All Comics</a>
        </div>
        <div class="trending-row" id="trendingComics">
          <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        </div>
      </section>

      <div class="genre-chips mb-3" id="genreChips">
        <button class="chip active" data-genre="">All</button>
        <button class="chip" data-genre="action">Action</button>
        <button class="chip" data-genre="romance">Romance</button>
        <button class="chip" data-genre="fantasy">Fantasy</button>
        <button class="chip" data-genre="isekai">Isekai</button>
        <button class="chip" data-genre="cultivation">Cultivation</button>
        <button class="chip" data-genre="system">System</button>
      </div>

      <main class="mb-4">
        <h4 class="section-title mb-4" id="gridTitle">All Comics</h4>
        <div id="grid" class="row g-4"></div>
      </main>
    </div>

    <aside class="col-lg-4">
      <div class="sidebar-widget mb-4">
        <div class="sw-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">📈 Popular</h5>
          <div class="btn-group btn-group-sm" id="popularTabs">
            <button class="btn btn-nv active" data-range="week">Weekly</button>
            <button class="btn btn-nv" data-range="month">Monthly</button>
            <button class="btn btn-nv" data-range="all">All Time</button>
          </div>
        </div>
        <ol class="popular-list" id="popularList">
          <li class="text-center py-4"><div class="spinner-border text-primary"></div></li>
        </ol>
      </div>

      <div class="sidebar-widget mb-4">
        <div class="sw-header">
          <h5 class="mb-0">🏆 Top Readers</h5>
        </div>
        <ol class="popular-list" id="topReaders">
          <li class="text-center py-4"><div class="spinner-border text-primary"></div></li>
        </ol>
      </div>
    </aside>
  </div>
</div>

<script src="/public/js/app.js?v=105"></script>
<script>
  // Inline SVG fallback to guarantee placeholder loads even if offline/blocked
  const DEFAULT_PLACEHOLDER = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='300' height='400' viewBox='0 0 300 400'><rect width='100%' height='100%' fill='%231e293b'/><text x='50%' y='50%' font-family='sans-serif' font-size='18' fill='%2364748b' text-anchor='middle'>No Cover Available</text></svg>";

  const PH = (typeof PLACEHOLDER !== 'undefined' && PLACEHOLDER)
    ? PLACEHOLDER
    : DEFAULT_PLACEHOLDER;

  /** Resolves proper cover image priority */
  function coverOf(s) {
    if (!s) return PH;
    let url = s.cover_url || s.cover || s._anilist_cover;
    if (typeof coverUrl === 'function' && url) {
      url = coverUrl(url);
    }
    return (url && url.trim() !== '') ? url : PH;
  }

  /** Evaluates if item needs an external fallback image query */
  function needsAniListCover(url) {
    if (!url || String(url).trim() === '') return true;
    const u = String(url).toLowerCase();
    if (u.includes('mangadex.org')) return false; 
    if (u.includes('anilist.co')) return false;
    if (!u.startsWith('http')) return true;
    return false;
  }

  const aniCoverCache = {};

  async function fetchAniListCover(title) {
    if (!title) return null;
    if (aniCoverCache[title] !== undefined) return aniCoverCache[title];

    const paths = [
      `/backend/api/anilist.php?action=search&q=${encodeURIComponent(title)}`,
      `/public/api/anilist.php?action=search&q=${encodeURIComponent(title)}`
    ];

    for (const path of paths) {
      try {
        const res = await fetch(path, { credentials: 'include' });
        if (!res.ok) continue;
        const json = await res.json();
        const hit = (json.data || [])[0];
        const cover = hit && (hit.cover || hit.coverImage || hit.cover_url);
        if (cover) {
          aniCoverCache[title] = cover;
          return cover;
        }
      } catch (e) { /* ignore & try fallback path */ }
    }
    aniCoverCache[title] = null;
    return null;
  }

  /** Efficiently enriches item list with AniList covers */
  async function enrichCovers(list) {
    const items = list || [];
    const jobs = items.map(async (s) => {
      const current = s.cover_url || s.cover;
      if (!needsAniListCover(current)) return s;
      const ani = await fetchAniListCover(s.title);
      if (ani) {
        s._anilist_cover = ani;
        if (!s.cover_url) s.cover_url = ani;
      }
      return s;
    });
    await Promise.all(jobs);
    return items;
  }

  function imgTag(src, extraClass = '') {
    const safeSrc = src || PH;
    return `<img
      src="${safeSrc}"
      class="${extraClass}"
      alt=""
      loading="lazy"
      referrerpolicy="no-referrer"
      onerror="this.onerror=null;this.src='${PH}'"
    >`;
  }

  // ---------- HERO SLIDESHOW ----------
  let heroSlides = [], heroIndex = 0;

  async function loadHero() {
    const heroEl = document.getElementById('heroSlideshow');
    try {
      const res = await api('series.php?sort=popular&limit=5');
      heroSlides = await enrichCovers(res.data || []);
      if (heroSlides.length) {
        renderHero();
        setInterval(nextHero, 5000);
      } else {
        heroEl.innerHTML = `
          <div class="hero-slide" style="background:#1e293b">
            <div class="hero-content"><h1>Manga, Manhwa &amp; Manhua</h1></div>
          </div>`;
      }
    } catch (e) {
      heroEl.innerHTML = `
        <div class="hero-slide" style="background:#1e293b">
          <div class="hero-content"><h1>Manga, Manhwa &amp; Manhua</h1></div>
        </div>`;
    }
  }

  function renderHero() {
    const s = heroSlides[heroIndex];
    if (!s) return;
    const bg = coverOf(s);
    document.getElementById('heroSlideshow').innerHTML = `
      <div class="hero-slide" style="background-image:url('${bg}')">
        <div class="hero-content">
          <span class="hero-badge">#${heroIndex + 1} Most Read</span>
          <h1 class="hero-title">${s.title || 'Untitled'}</h1>
          <p class="hero-desc">${(s.description || s.desc || '').slice(0, 150)}…</p>
          <a href="series.php?id=${encodeURIComponent(s.id || '')}&slug=${encodeURIComponent(s.slug || '')}" class="btn btn-nv btn-lg">Read Now</a>
        </div>
      </div>`;
  }

  function nextHero() {
    if (!heroSlides.length) return;
    heroIndex = (heroIndex + 1) % heroSlides.length;
    renderHero();
  }

  // ---------- TRENDING ----------
  async function loadTrending(targetId, params) {
    const el = document.getElementById(targetId);
    if (!el) return;
    try {
      const res = await api(`series.php?${params}`);
      const list = await enrichCovers(res.data || []);
      el.innerHTML = list.length
        ? list.map(trendingCard).join('')
        : '<p class="text-muted-nv px-3">Nothing here yet — import some series!</p>';
    } catch (e) {
      el.innerHTML = `<p class="text-danger px-3">Error: ${e.message}</p>`;
    }
  }

  function trendingCard(s) {
    const stars = '★★★★★'.slice(0, Math.round((s.rating || 0) / 2));
    const chapterLabel = s.source === 'anilist'
      ? '<div class="az-sub">Info only</div>'
      : `<div class="az-sub">Chapter ${s.latest_chapter ?? '?'}</div>`;

    const targetUrl = s.id 
      ? `series.php?id=${encodeURIComponent(s.id)}`
      : `series.php?slug=${encodeURIComponent(s.slug || '')}`;

    return `
    <a href="${targetUrl}" class="az-card">
      <div class="az-cover">
        ${imgTag(coverOf(s))}
      </div>
      <div class="az-title">${s.title || 'Untitled'}</div>
      ${chapterLabel}
      <div class="az-rating">
        <span class="az-stars">${stars || '☆☆☆☆☆'}</span>
        <span class="az-score">${s.rating ? Number(s.rating).toFixed(1) : 'N/A'}</span>
      </div>
    </a>`;
  }

  // ---------- POPULAR ----------
  async function loadPopular(range = 'week') {
    const el = document.getElementById('popularList');
    try {
      const res = await api(`series.php?sort=popular&range=${range}&limit=10`);
      const list = await enrichCovers(res.data || []);
      el.innerHTML = list.map((s, i) => {
        const targetUrl = s.id 
          ? `series.php?id=${encodeURIComponent(s.id)}`
          : `series.php?slug=${encodeURIComponent(s.slug || '')}`;

        return `
        <li class="popular-item">
          <span class="rank ${i < 3 ? 'rank-top' : ''}">${i + 1}</span>
          ${imgTag(coverOf(s), 'pi-cover')}
          <div class="pi-info">
            <a href="${targetUrl}" class="pi-title">${s.title || 'Untitled'}</a>
            <div class="pi-meta">⭐ ${s.rating ? Number(s.rating).toFixed(1) : 'N/A'}</div>
          </div>
        </li>`;
      }).join('') || '<li class="text-muted-nv px-3 py-2">No data yet.</li>';
    } catch (e) {
      el.innerHTML = `<li class="text-danger px-3">Error: ${e.message}</li>`;
    }
  }

  // ---------- TOP READERS ----------
  async function loadTopReaders() {
    const el = document.getElementById('topReaders');
    try {
      const res = await api('users.php?sort=reading&limit=10');
      el.innerHTML = (res.data || []).map((u, i) => `
        <li class="popular-item">
          <span class="rank ${i < 3 ? 'rank-top' : ''}">${i + 1}</span>
          <div class="pi-info">
            <span class="pi-title">${u.username}</span>
            <div class="pi-meta">${u.chapters_read ?? 0} chapters read</div>
          </div>
        </li>`).join('') || '<li class="text-muted-nv px-3 py-2">No readers ranked yet.</li>';
    } catch (e) {
      el.innerHTML = '<li class="text-muted-nv px-3 py-2">No readers ranked yet.</li>';
    }
  }

  // ---------- ALL COMICS GRID ----------
  let activeGenre = '';

  async function loadGrid() {
    const grid = document.getElementById('grid');
    try {
      const res = await api('series.php' + (activeGenre ? `?genre=${encodeURIComponent(activeGenre)}` : ''));
      const list = await enrichCovers(res.data || []);

      grid.innerHTML = list.map(s => {
        if (typeof seriesCard === 'function') return seriesCard(s);

        const targetUrl = s.id 
          ? `series.php?id=${encodeURIComponent(s.id)}`
          : `series.php?slug=${encodeURIComponent(s.slug || '')}`;

        return `
          <div class="col-6 col-md-4 col-lg-3">
            <a class="nv-card h-100 d-block" href="${targetUrl}">
              <div class="card-cover">
                ${imgTag(coverOf(s))}
                <span class="type-badge ${(s.type || 'manga')}">${(s.type || 'manga').toUpperCase()}</span>
              </div>
              <div class="card-body">
                <h3>${s.title || 'Untitled'}</h3>
                <p>${s.status || ''}</p>
              </div>
            </a>
          </div>`;
      }).join('') || '<p class="text-muted-nv">No series in this genre yet.</p>';
    } catch (e) {
      grid.innerHTML = `<div class="col-12 alert alert-danger"><b>Error:</b> ${e.message}</div>`;
    }
  }

  // Event Listeners
  document.getElementById('genreChips').addEventListener('click', e => {
    if (!e.target.classList.contains('chip')) return;
    document.querySelectorAll('#genreChips .chip').forEach(c => c.classList.remove('active'));
    e.target.classList.add('active');
    activeGenre = e.target.dataset.genre || '';
    document.getElementById('gridTitle').textContent = activeGenre
      ? activeGenre.charAt(0).toUpperCase() + activeGenre.slice(1) + ' Comics'
      : 'All Comics';
    loadGrid();
  });

  document.getElementById('popularTabs').addEventListener('click', e => {
    if (!e.target.dataset.range) return;
    document.querySelectorAll('#popularTabs .btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    loadPopular(e.target.dataset.range);
  });

  // Initial Data Loads
  loadHero().catch(console.error);
  loadTrending('trendingComics', 'sort=trending&limit=12').catch(console.error);
  loadPopular('week').catch(console.error);
  loadTopReaders().catch(console.error);
  loadGrid().catch(console.error);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
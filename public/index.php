<?php $pageTitle = 'Home'; require_once __DIR__ . '/includes/header.php'; ?>

<!-- ============ HERO SLIDESHOW (most read series) ============ -->
<section class="hero-section mb-4">
  <div id="heroSlideshow" class="hero-slideshow">
    <div class="hero-loading text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>
  </div>
</section>

<!-- ============ MAIN LAYOUT: content + sidebar ============ -->
<div class="container">
  <div class="row g-4">

    <!-- LEFT COLUMN -->
    <div class="col-lg-8">

      <!-- Trending Comics -->
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

      <!-- Trending Novels -->
      <section class="trending-panel mb-4">
        <div class="trending-header">
          <div class="th-left">
            <div class="th-icon">📚</div>
            <h2>Trending Novels</h2>
          </div>
          <a href="browse.php?type=novel" class="th-all btn btn-sm btn-nv">All Novels</a>
        </div>
        <div class="trending-row" id="trendingNovels">
          <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
        </div>
      </section>

      <!-- Genre chips -->
      <div class="genre-chips mb-3" id="genreChips">
        <button class="chip active" data-genre="">All</button>
        <button class="chip" data-genre="action">Action</button>
        <button class="chip" data-genre="romance">Romance</button>
        <button class="chip" data-genre="fantasy">Fantasy</button>
        <button class="chip" data-genre="isekai">Isekai</button>
        <button class="chip" data-genre="cultivation">Cultivation</button>
        <button class="chip" data-genre="system">System</button>
      </div>

      <!-- All of Comics -->
      <main class="mb-4">
        <h4 class="section-title mb-4" id="gridTitle">All of Comics</h4>
        <div id="grid" class="row g-4"></div>
      </main>
    </div>

    <!-- RIGHT SIDEBAR -->
    <aside class="col-lg-4">

      <!-- Popular series with time tabs -->
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

      <!-- Top Readers leaderboard -->
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

<script src="js/app.js"></script>
<script>
  // ---------- HERO SLIDESHOW ----------
  let heroSlides = [], heroIndex = 0;
  async function loadHero() {
    try {
      const res = await api('series.php?sort=popular&limit=5');
      heroSlides = res.data;
      if (heroSlides.length) { renderHero(); setInterval(nextHero, 5000); }
      else document.getElementById('heroSlideshow').innerHTML =
        '<div class="hero-slide" style="background:#1e293b"><div class="hero-content"><h1>Read Comics & Light Novels</h1></div></div>';
    } catch (e) {
      document.getElementById('heroSlideshow').innerHTML =
        '<div class="hero-slide" style="background:#1e293b"><div class="hero-content"><h1>Read Comics & Light Novels</h1></div></div>';
    }
  }
  function renderHero() {
    const s = heroSlides[heroIndex];
    document.getElementById('heroSlideshow').innerHTML = `
      <div class="hero-slide" style="background-image:url('${s.cover_url || ''}')">
        <div class="hero-content">
          <span class="hero-badge">#${heroIndex + 1} Most Read</span>
          <h1 class="hero-title">${s.title}</h1>
          <p class="hero-desc">${(s.description || '').slice(0, 150)}…</p>
          <a href="series.php?id=${s.id}" class="btn btn-nv btn-lg">Read Now</a>
        </div>
      </div>`;
  }
  function nextHero() { heroIndex = (heroIndex + 1) % heroSlides.length; renderHero(); }

  // ---------- TRENDING ROWS ----------
  async function loadTrending(targetId, params) {
    const el = document.getElementById(targetId);
    try {
      const res = await api(`series.php?${params}`);
      el.innerHTML = res.data.length
        ? res.data.map(trendingCard).join('')
        : '<p class="text-muted-nv px-3">Nothing here yet — import some series!</p>';
    } catch (e) { el.innerHTML = `<p class="text-danger px-3">Error: ${e.message}</p>`; }
  }

  // Card format per reference: cover → title → Chapter N → rating
  function trendingCard(s) {
  const stars = '★★★★★'.slice(0, Math.round(s.rating || 0) / 2); // rough 5-star display
  const chapterLabel = s.source === 'anilist'
    ? '<div class="az-sub">Info only — no chapters</div>'
    : `<div class="az-sub">Chapter ${s.latest_chapter ?? '?'}</div>`;
  return `
    <a href="series.php?slug=${s.slug}" class="az-card">
      <div class="az-cover">
        <img src="${s.cover_url || 'assets/no-cover.png'}" alt="${s.title}" loading="lazy">
      </div>
      <div class="az-title">${s.title}</div>
      ${chapterLabel}
      <div class="az-rating">
        <span class="az-stars">${stars || '☆☆☆☆☆'}</span>
        <span class="az-score">${s.rating ? Number(s.rating).toFixed(1) : 'N/A'}</span>
      </div>
    </a>`;
}

  // ---------- POPULAR SIDEBAR (Weekly / Monthly / All Time) ----------
  async function loadPopular(range = 'week') {
    const el = document.getElementById('popularList');
    try {
      const res = await api(`series.php?sort=popular&range=${range}&limit=10`);
      el.innerHTML = res.data.map((s, i) => `
        <li class="popular-item">
          <span class="rank ${i < 3 ? 'rank-top' : ''}">${i + 1}</span>
          <img src="${s.cover_url || 'assets/no-cover.png'}" class="pi-cover">
          <div class="pi-info">
            <a href="series.php?id=${s.id}" class="pi-title">${s.title}</a>
            <div class="pi-meta">⭐ ${s.rating ? Number(s.rating).toFixed(1) : 'N/A'}</div>
          </div>
        </li>`).join('') || '<li class="text-muted-nv px-3 py-2">No data yet.</li>';
    } catch (e) { el.innerHTML = `<li class="text-danger px-3">Error: ${e.message}</li>`; }
  }

  // ---------- TOP READERS ----------
  async function loadTopReaders() {
    const el = document.getElementById('topReaders');
    try {
      const res = await api('users.php?sort=reading&limit=10');
      el.innerHTML = res.data.map((u, i) => `
        <li class="popular-item">
          <span class="rank ${i < 3 ? 'rank-top' : ''}">${i + 1}</span>
          <img src="${u.avatar_url || 'assets/default-avatar.png'}" class="pi-cover rounded-circle">
          <div class="pi-info">
            <span class="pi-title">${u.username}</span>
            <div class="pi-meta">${u.chapters_read ?? 0} chapters read</div>
          </div>
        </li>`).join('') || '<li class="text-muted-nv px-3 py-2">No readers ranked yet.</li>';
    } catch (e) { el.innerHTML = `<li class="text-danger px-3">Error: ${e.message}</li>`; }
  }

  // ---------- ALL OF COMICS GRID + GENRE FILTER ----------
  let activeGenre = '';
  async function loadGrid() {
    const grid = document.getElementById('grid');
    try {
      const res = await api('series.php' + (activeGenre ? `?genre=${activeGenre}` : ''));
      grid.innerHTML = res.data.map(seriesCard).join('') ||
        '<p class="text-muted-nv">No series in this genre yet.</p>';
    } catch (e) {
      grid.innerHTML = `<div class="col-12 alert alert-danger"><b>Error:</b> ${e.message}</div>`;
    }
  }

  document.getElementById('genreChips').addEventListener('click', e => {
    if (!e.target.classList.contains('chip')) return;
    document.querySelectorAll('#genreChips .chip').forEach(c => c.classList.remove('active'));
    e.target.classList.add('active');
    activeGenre = e.target.dataset.genre;
    document.getElementById('gridTitle').textContent =
      activeGenre ? activeGenre.charAt(0).toUpperCase() + activeGenre.slice(1) + ' Comics' : 'All of Comics';
    loadGrid();
  });

  document.getElementById('popularTabs').addEventListener('click', e => {
    if (!e.target.dataset.range) return;
    document.querySelectorAll('#popularTabs .btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    loadPopular(e.target.dataset.range);
  });

  // ---------- BOOT ----------
  loadHero();
  loadTrending('trendingComics', 'sort=trending&type=comic&limit=12');
  loadTrending('trendingNovels', 'sort=trending&type=novel&limit=12');
  loadPopular('week');
  loadTopReaders();
  loadGrid();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
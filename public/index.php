<?php $pageTitle = 'Home'; require_once __DIR__ . '/includes/header.php'; ?>

<section class="hero-section py-5">
  <div class="container text-center py-4">
    <h1 class="display-5 fw-bold text-white">Read Comics &amp; Light Novels</h1>
    <p class="lead text-muted-nv">Manga • Manhwa • Manhua • Light Novels — powered by MangaDex</p>
    <a href="browse.php" class="btn btn-nv btn-lg px-4 mt-2">
      <i class="bi bi-collection-play me-2"></i>Start Reading
    </a>
  </div>

<section class="container my-5">
  <div class="trending-panel">
    <div class="trending-header">
      <div class="th-left">
        <div class="th-icon">🔥</div>
        <h2>Trending Manhwa</h2>
      </div>
      <a href="browse.php" class="th-all">View All</a>
    </div>
    <div class="trending-row" id="trendingGrid">
      <div class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2 text-muted">Loading trending manhwa...</p>
      </div>
    </div>
  </div>
</section>

<main class="container py-5">
  <h4 class="section-title mb-4">Latest Series</h4>
  <div id="grid" class="row g-4"></div>
</main>

<script src="js/app.js"></script>
<script>
  async function load() {
    try {
      const res = await api('series.php');
      document.getElementById('grid').innerHTML =
        res.data.map(seriesCard).join('') ||
        '<p class="text-muted-nv">No series yet. Import from MangaDex in Browse.</p>';
    } catch (e) {
      document.getElementById('grid').innerHTML =
        `<div class="col-12 alert alert-danger">
           <b>Error loading series:</b> ${e.message}
           ${e.status ? `(HTTP ${e.status})` : ''}
         </div>`;
    }
  }
  load();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
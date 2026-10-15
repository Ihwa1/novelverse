<?php $pageTitle = 'Read Novel'; require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-4" id="novel">
  <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
</main>

<div class="modal fade" id="payModal" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-header border-0">
        <h5 class="modal-title text-white"><i class="bi bi-lock-fill me-2"></i>Unlock Full Book</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p id="payInfo" class="text-muted-nv"></p>
        <h3 class="text-white mb-3" id="payPrice"></h3>
        <button class="btn btn-gcash w-100 mb-2 py-2" onclick="pay('gcash')">
          <i class="bi bi-wallet2 me-2"></i>Pay with GCash
        </button>
        <button class="btn btn-card-pay w-100 py-2" onclick="pay('card')">
          <i class="bi bi-credit-card me-2"></i>Debit / Credit Card
        </button>
        <small class="text-muted-nv d-block mt-3">
          <i class="bi bi-shield-lock me-1"></i>One-time purchase — unlocks ALL remaining chapters forever
        </small>
      </div>
    </div>
  </div>
</div>

<script src="js/app.js"></script>
<script>
  const params = new URLSearchParams(location.search);
  const slug = params.get('slug');
  let series;

  async function load() {
    const res = await api(`series.php?slug=${slug}`);
    series = res.data;

    document.getElementById('novel').innerHTML = `
      <div class="row g-4 mb-4">
        <div class="col-md-3 col-5">
          <img src="${series.cover_url}" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-9 col-7">
          <span class="type-badge novel inline">NOVEL</span>
          <h1 class="text-white mt-2">${series.title}</h1>
          <p class="text-muted-nv">${series.description || ''}</p>
          ${series.owned
            ? `<span class="tag owned"><i class="bi bi-check-circle me-1"></i>PURCHASED — ALL CHAPTERS UNLOCKED</span>`
            : `<button class="btn btn-gcash mt-2" onclick="openPaywall()">
                 <i class="bi bi-bag me-1"></i>Buy Full Book — ₱${series.novel_price}</button>`}
        </div>
      </div>
      <h4 class="section-title mb-3">Chapters</h4>
      <div id="chList"></div>`;

    document.getElementById('chList').innerHTML = series.chapters.map(c => `
      <a class="chapter-item" href="novel.php?slug=${slug}&ch=${c.id}">
        <span><i class="bi bi-file-text me-2 text-primary"></i>
          Chapter ${c.chapter_number} — ${c.title || ''}</span>
        ${c.is_free
          ? '<span class="tag free">FREE</span>'
          : (series.owned
              ? '<span class="tag owned">UNLOCKED</span>'
              : '<span class="tag locked"><i class="bi bi-lock-fill"></i> PURCHASE</span>')}
      </a>`).join('');

    const ch = params.get('ch');
    if (ch) readChapter(ch);
  }

  async function readChapter(id) {
    try {
      const res = await api(`chapters.php?id=${id}`);
      document.getElementById('novel').innerHTML = `
        <a href="novel.php?slug=${slug}" class="btn btn-outline-nv btn-sm mb-3">
          <i class="bi bi-arrow-left me-1"></i>Back to chapters</a>
        <h2 class="text-white">Chapter ${res.data.number} ${res.data.title || ''}</h2>
        <hr style="border-color:var(--nv-navy-700)">
        <div class="novel-text py-4">${res.data.content.replace(/\n/g, '<br>')}</div>`;
      window.scrollTo(0, 0);
    } catch (e) {
      if (e.status === 402 && e.json.locked) openPaywall(e.json);
    }
  }

  function openPaywall(info) {
    document.getElementById('payInfo').textContent =
      info?.message || 'Free preview ended. Buy the full book to unlock all remaining chapters.';
    document.getElementById('payPrice').textContent = '₱' + (info?.price || series.novel_price);
    new bootstrap.Modal('#payModal').show();
  }

  async function pay(method) {
    try {
      await buyNovel(series.id, method);
    } catch (e) { alert(e.message); }
  }

  load();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
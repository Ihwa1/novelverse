
<?php
require_once __DIR__ . '/../includes/init.php';

if (!$nv_user || ($nv_user['role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$pageTitle = 'Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
  /* Hide the public navbar on the admin dashboard */
  body:has(.nv-admin-layout) > nav.nv-navbar {
    display: none;
  }

  .nv-admin-layout {
    --admin-accent: #f59e0b;
    --admin-bg: #0f111a;
    min-height: 100vh;
    background: var(--admin-bg);
    color: #fff;
  }

  .nv-admin-sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    z-index: 1030;
    display: flex;
    flex-direction: column;
    width: 255px;
    padding: 26px 16px;
    overflow-y: auto;
    background: #141620;
    border-right: 1px solid rgba(255,255,255,.08);
  }

  .nv-admin-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px 24px;
    color: #fff;
    font-size: 19px;
    font-weight: 800;
    letter-spacing: 1px;
    text-decoration: none;
  }

  .nv-admin-brand i {
    color: var(--admin-accent);
    font-size: 23px;
  }

  .nv-admin-label {
    padding: 12px;
    color: #8d91a2;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
  }

  .nv-admin-nav {
    display: flex;
    flex-direction: column;
    gap: 7px;
  }

  .nv-admin-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 13px;
    border-radius: 9px;
    color: #b8bbc8;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: background .2s, color .2s;
  }

  .nv-admin-link i {
    width: 20px;
    font-size: 17px;
    text-align: center;
  }

  .nv-admin-link:hover,
  .nv-admin-link.active {
    color: #fff;
    background: rgba(245,158,11,.16);
  }

  .nv-admin-link.active {
    box-shadow: inset 3px 0 var(--admin-accent);
  }

  .nv-admin-sidebar-bottom {
    margin-top: auto;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.08);
  }

  .nv-admin-content {
    min-height: 100vh;
    margin-left: 255px;
    padding: 36px;
  }

  .nv-admin-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 30px;
  }

  .nv-admin-eyebrow {
    color: var(--admin-accent);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
  }

  .nv-admin-header h2 {
    margin: 7px 0;
    color: #fff;
    font-size: clamp(25px, 3vw, 32px);
    font-weight: 750;
  }

  .nv-admin-header p {
    margin: 0;
    color: #9ca3af;
  }

  .nv-admin-role {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 13px;
    border: 1px solid rgba(245,158,11,.3);
    border-radius: 9px;
    background: rgba(245,158,11,.1);
    color: #fbbf24;
    font-size: 13px;
    font-weight: 600;
  }

  .nv-admin-card {
    border-radius: 12px;
    scroll-margin-top: 24px;
  }

  #your-series {
    scroll-margin-top: 24px;
  }

  .nv-admin-content .form-control,
  .nv-admin-content .form-select {
    min-height: 42px;
  }

  .nv-admin-content textarea.form-control {
    min-height: auto;
  }

  .nv-admin-content .btn-primary {
    background: #b7790b;
    border-color: #d99a20;
  }

  .nv-admin-content .btn-primary:hover {
    background: #d18a0b;
    border-color: #fbbf24;
  }

  /* Existing modal styles */
  .nv-modal-backdrop[hidden] {
    display: none !important;
  }

  .nv-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(0,0,0,.72);
    backdrop-filter: blur(4px);
  }

  .nv-modal {
    width: 100%;
    max-width: 420px;
    padding: 32px 26px;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 20px;
    background: #171923;
    color: #fff;
    text-align: center;
    box-shadow: 0 24px 70px rgba(0,0,0,.45);
    animation: nvModalIn .2s ease-out;
  }

  .nv-modal-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: rgba(25,135,84,.18);
    color: #4ade80;
    font-size: 34px;
    font-weight: 700;
  }

  .nv-modal-icon.nv-modal-error {
    background: rgba(220,53,69,.18);
    color: #ff6b7a;
  }

  .nv-modal h4 {
    margin-bottom: 12px;
    font-weight: 700;
  }

  .nv-modal p {
    margin-bottom: 24px;
    color: #c4c7d0;
    line-height: 1.6;
    overflow-wrap: anywhere;
  }

  @keyframes nvModalIn {
    from {
      opacity: 0;
      transform: translateY(12px) scale(.97);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .nv-modal {
      animation: none;
    }
  }

  @media (max-width: 768px) {
    .nv-admin-sidebar {
      width: 76px;
      padding: 20px 10px;
    }

    .nv-admin-brand {
      justify-content: center;
      padding: 8px 0 24px;
    }

    .nv-admin-brand span,
    .nv-admin-label,
    .nv-admin-link span {
      display: none;
    }

    .nv-admin-link {
      justify-content: center;
      padding: 13px 8px;
    }

    .nv-admin-link i {
      font-size: 20px;
    }

    .nv-admin-content {
      margin-left: 76px;
      padding: 24px 16px;
    }
  }

  @media (max-width: 420px) {
    .nv-admin-content {
      padding: 20px 12px;
    }

    .nv-admin-role {
      font-size: 12px;
    }
  }
  
/* Overview statistic cards */
.nv-stat-card {
  height: 100%;
  padding: 20px;
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 13px;
  background: #171923;
  transition: transform .2s ease, border-color .2s ease;
}

.nv-stat-card:hover {
  transform: translateY(-3px);
  border-color: rgba(245,158,11,.35);
}

.nv-stat-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  color: #aeb2c0;
  font-size: 13px;
}

.nv-stat-top i {
  font-size: 23px;
}

.nv-stat-card h3 {
  margin: 18px 0 5px;
  color: #fff;
  font-size: 30px;
  font-weight: 750;
  overflow-wrap: anywhere;
}

.nv-stat-card p {
  margin: 0;
  color: #858b9b;
  font-size: 12px;
}

.nv-stat-blue {
  color: #60a5fa;
}

.nv-stat-purple {
  color: #a78bfa;
}

.nv-stat-green {
  color: #34d399;
}

.nv-stat-amber {
  color: #fbbf24;
}

/* Quick action cards */
.nv-quick-card {
  display: flex;
  align-items: center;
  gap: 13px;
  height: 100%;
  min-height: 100px;
  padding: 17px;
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 12px;
  background: #171923;
  color: #fff;
  text-decoration: none;
  transition: background .2s ease, border-color .2s ease,
              transform .2s ease;
}

.nv-quick-card:hover {
  background: #202331;
  border-color: rgba(245,158,11,.4);
  color: #fff;
  transform: translateY(-2px);
}

.nv-quick-card > i:first-child {
  color: #fbbf24;
  font-size: 25px;
}

.nv-quick-card > div {
  display: flex;
  flex: 1;
  flex-direction: column;
  gap: 5px;
  min-width: 0;
}

.nv-quick-card strong {
  font-size: 13px;
}

.nv-quick-card small {
  color: #9ca3af;
  font-size: 11px;
  line-height: 1.5;
}

.nv-quick-arrow {
  color: #777e90;
  font-size: 14px;
}
#overview-stats .nv-stat-card {
  min-width: 0;
  padding: 16px 12px;
}

#overview-stats .nv-stat-top {
  align-items: flex-start;
  font-size: 12px;
}

#overview-stats .nv-stat-card h3 {
  font-size: clamp(20px, 2vw, 30px);
}

@media (max-width: 768px) {
  #overview-stats .row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  #overview-stats .row > * {
    width: auto;
  }
}
</style>

<div class="nv-admin-layout">

  <aside class="nv-admin-sidebar">
    <a href="../index.php" class="nv-admin-brand">
      <i class="bi bi-moon-stars-fill"></i>
      <span>NOVELVERSE</span>
    </a>

    <div class="nv-admin-label">ADMIN PANEL</div>

    <nav class="nv-admin-nav">
      <a href="#overview" class="nv-admin-link active">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Overview</span>
      </a>

      <a href="#add-series" class="nv-admin-link">
        <i class="bi bi-journal-plus"></i>
        <span>Add Series</span>
      </a>

      <a href="#add-chapter" class="nv-admin-link">
        <i class="bi bi-file-earmark-plus"></i>
        <span>Add Chapter</span>
      </a>

      <a href="#applications" class="nv-admin-link">
        <i class="bi bi-person-check"></i>
        <span>Author Applications</span>
      </a>

      <a href="#your-series" class="nv-admin-link">
        <i class="bi bi-collection"></i>
        <span>Your Series</span>
      </a>
    </nav>

    <div class="nv-admin-sidebar-bottom">
      <a href="../index.php" class="nv-admin-link">
        <i class="bi bi-arrow-left-circle"></i>
        <span>Back to Home</span>
      </a>
    </div>
  </aside>

  <main class="nv-admin-content">

    <header class="nv-admin-header" id="overview">
      <div>
        <span class="nv-admin-eyebrow">ADMINISTRATION</span>
        <h2>Dashboard</h2>
        <p>Manage series, chapters, and author applications.</p>
      </div>

      <span class="nv-admin-role">
        <i class="bi bi-shield-lock-fill"></i>
        Administrator
      </span>
    </header>

    <div id="msg" class="alert d-none"></div>
    
<!-- OVERVIEW STATISTICS -->
<section id="overview-stats" class="mb-4">
  <h5 class="text-white mb-3">Platform Overview</h5>

  <div class="row row-cols-5 g-3">
    <div class="col">
      <div class="nv-stat-card">
        <div class="nv-stat-top">
          <span>Total Users</span>
          <i class="bi bi-people-fill nv-stat-blue"></i>
        </div>
        <h3 id="totalUsers">—</h3>
        <p>Registered accounts</p>
      </div>
    </div>
    
    <div class="col">
      <div class="nv-stat-card">
        <div class="nv-stat-top">
          <span>Total Authors</span>
          <i class="bi bi-pen-fill nv-stat-purple"></i>
        </div>
        <h3 id="totalAuthors">—</h3>
        <p>Accounts with Author access</p>
      </div>
    </div>

    <div class="col">
      <div class="nv-stat-card">
        <div class="nv-stat-top">
          <span>Total Series</span>
          <i class="bi bi-book-half nv-stat-purple"></i>
        </div>
        <h3 id="totalSeries">—</h3>
        <p>Titles in the library</p>
      </div>
    </div>

    <div class="col">
      <div class="nv-stat-card">
        <div class="nv-stat-top">
          <span>Total Chapters</span>
          <i class="bi bi-files nv-stat-green"></i>
        </div>
        <h3 id="totalChapters">—</h3>
        <p>Chapters in the library</p>
      </div>
    </div>

    <div class="col">
      <div class="nv-stat-card">
        <div class="nv-stat-top">
          <span>Pending Applications</span>
          <i class="bi bi-person-check-fill nv-stat-amber"></i>
        </div>
        <h3 id="pendingApplications">—</h3>
        <p>Awaiting your review</p>
      </div>
    </div>
  </div>
</section>

<!-- QUICK ACTIONS -->
<section class="nv-quick-actions mb-4">
  <h5 class="text-white mb-3">Quick Actions</h5>

  <div class="row g-3">
    <div class="col-12 col-md-4">
      <a href="#add-series" class="nv-quick-card">
        <i class="bi bi-plus-circle-fill"></i>
        <div>
          <strong>Add a Series</strong>
          <small>Create a new title for the library.</small>
        </div>
        <i class="bi bi-arrow-up-right nv-quick-arrow"></i>
      </a>
    </div>

    <div class="col-12 col-md-4">
      <a href="#add-chapter" class="nv-quick-card">
        <i class="bi bi-file-earmark-plus-fill"></i>
        <div>
          <strong>Add a Chapter</strong>
          <small>Add pages or novel text.</small>
        </div>
        <i class="bi bi-arrow-up-right nv-quick-arrow"></i>
      </a>
    </div>

    <div class="col-12 col-md-4">
      <a href="#applications" class="nv-quick-card">
        <i class="bi bi-person-check-fill"></i>
        <div>
          <strong>Review Applications</strong>
          <small>Manage author requests.</small>
        </div>
        <i class="bi bi-arrow-up-right nv-quick-arrow"></i>
      </a>
    </div>
  </div>
</section>

    <!-- ADD SERIES -->
    <section class="card bg-dark border-secondary mb-4 nv-admin-card"
             id="add-series">
      <div class="card-body">
        <h5 class="text-white mb-3">
          <i class="bi bi-journal-plus text-warning me-2"></i>
          Add Series
        </h5>

        <form id="seriesForm" class="row g-2">
          <div class="col-md-6">
            <input name="title" class="form-control"
                   placeholder="Title" required>
          </div>

          <div class="col-md-3">
            <select name="type" class="form-select">
              <option value="manga">Manga</option>
              <option value="manhwa">Manhwa</option>
              <option value="manhua">Manhua</option>
              <option value="novel">Novel</option>
            </select>
          </div>

          <div class="col-md-3">
            <input name="status" class="form-control"
                   placeholder="Status (ongoing)" value="ongoing">
          </div>

          <div class="col-12">
            <input name="cover_url" class="form-control"
                   placeholder="Cover URL">
          </div>

          <div class="col-12">
            <textarea name="description" class="form-control"
                      rows="3" placeholder="Description"></textarea>
          </div>

          <div class="col-auto">
            <button class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Save Series
            </button>
          </div>
        </form>
      </div>
    </section>

    <!-- ADD CHAPTER -->
    <section class="card bg-dark border-secondary mb-4 nv-admin-card"
             id="add-chapter">
      <div class="card-body">
        <h5 class="text-white mb-3">
          <i class="bi bi-file-earmark-plus text-warning me-2"></i>
          Add Chapter
        </h5>

        <form id="chapterForm" class="row g-2">
          <div class="col-md-6">
            <select name="series_id" id="seriesSelect"
                    class="form-select" required>
              <option value="">Select a series</option>
            </select>
          </div>

          <div class="col-md-2">
            <input name="chapter_number" type="number" step="0.1"
                   class="form-control" placeholder="Ch #" value="1">
          </div>

          <div class="col-md-4">
            <input name="title" class="form-control"
                   placeholder="Chapter title">
          </div>

          <div class="col-12">
            <textarea name="pages" class="form-control" rows="5"
                      placeholder="Page image URLs — one per line"></textarea>
          </div>

          <div class="col-12">
            <textarea name="content" class="form-control" rows="3"
                      placeholder="Text content (novels only)"></textarea>
          </div>

          <div class="col-auto form-check align-self-center ms-2">
            <input class="form-check-input" type="checkbox"
                   name="is_free" id="isFree" checked>
            <label class="form-check-label text-white" for="isFree">
              Free chapter
            </label>
          </div>

          <div class="col-auto">
            <button class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Save Chapter
            </button>
          </div>
        </form>
      </div>
    </section>

    <!-- AUTHOR APPLICATIONS -->
    <section class="card bg-dark border-secondary mb-4 nv-admin-card"
             id="applications">
      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between
                    align-items-center gap-2 mb-3">
          <h5 class="text-white mb-0">
            <i class="bi bi-person-check text-warning me-2"></i>
            Author Applications
          </h5>

          <button type="button" class="btn btn-sm btn-outline-light"
                  id="refreshApplications">
            <i class="bi bi-arrow-clockwise"></i> Refresh
          </button>
        </div>

        <div id="applicationList">
          <p class="text-muted-nv">Loading applications...</p>
        </div>
      </div>
    </section>

    <!-- YOUR SERIES -->
    <section class="card bg-dark border-secondary mb-4 nv-admin-card"
             id="your-series">
      <div class="card-body">
        <h5 class="text-white mb-3">
          <i class="bi bi-collection text-warning me-2"></i>
          Your Series
        </h5>

        <div id="seriesList">
          <p class="text-muted-nv">Loading series...</p>
        </div>
      </div>
    </section>

  </main>
</div>

<!-- SUCCESS / ERROR MODAL -->
<div id="nvMessageModal" class="nv-modal-backdrop" hidden>
  <div class="nv-modal" role="dialog" aria-modal="true"
       aria-labelledby="nvMessageTitle"
       aria-describedby="nvMessageText">

    <div id="nvMessageIcon" class="nv-modal-icon">✓</div>
    <h4 id="nvMessageTitle">Success</h4>
    <p id="nvMessageText"></p>

    <button type="button" id="nvMessageClose"
            class="btn btn-success px-4">OK</button>
  </div>
</div>

<!-- CONFIRMATION MODAL -->
<div id="nvConfirmModal" class="nv-modal-backdrop" hidden>
  <div class="nv-modal" role="dialog" aria-modal="true"
       aria-labelledby="nvConfirmTitle">

    <div class="nv-modal-icon">?</div>
    <h4 id="nvConfirmTitle">Confirm Action</h4>
    <p id="nvConfirmText"></p>

    <div class="d-flex justify-content-center gap-2">
      <button type="button" id="nvConfirmCancel"
              class="btn btn-outline-light">Cancel</button>
      <button type="button" id="nvConfirmOK"
              class="btn btn-success">Continue</button>
    </div>
  </div>
</div>

<script src="../js/app.js"></script>

<script>
  const messageModal = document.getElementById('nvMessageModal');
  const messageTitle = document.getElementById('nvMessageTitle');
  const messageText = document.getElementById('nvMessageText');
  const messageIcon = document.getElementById('nvMessageIcon');
  const messageClose = document.getElementById('nvMessageClose');

  function show(message, ok = true, title = '') {
    messageTitle.textContent = title || (
      ok ? 'Action Completed' : 'Something Went Wrong'
    );

    messageText.textContent = message;
    messageIcon.textContent = ok ? '✓' : '!';
    messageIcon.classList.toggle('nv-modal-error', !ok);

    messageClose.className = ok
      ? 'btn btn-success px-4'
      : 'btn btn-danger px-4';

    messageModal.hidden = false;
    messageClose.focus();
  }

  const confirmModal = document.getElementById('nvConfirmModal');
  const confirmText = document.getElementById('nvConfirmText');
  const confirmOK = document.getElementById('nvConfirmOK');
  const confirmCancel = document.getElementById('nvConfirmCancel');

  function askConfirmation(message, buttonText = 'Continue') {
    return new Promise(resolve => {
      confirmText.textContent = message;
      confirmOK.textContent = buttonText;
      confirmModal.hidden = false;
      confirmOK.focus();

      function finish(result) {
        confirmModal.hidden = true;
        confirmOK.removeEventListener('click', onConfirm);
        confirmCancel.removeEventListener('click', onCancel);
        confirmModal.removeEventListener('click', onBackdrop);
        document.removeEventListener('keydown', onKeydown);
        resolve(result);
      }

      function onConfirm() {
        finish(true);
      }

      function onCancel() {
        finish(false);
      }

      function onBackdrop(event) {
        if (event.target === confirmModal) finish(false);
      }

      function onKeydown(event) {
        if (event.key === 'Escape') finish(false);
      }

      confirmOK.addEventListener('click', onConfirm);
      confirmCancel.addEventListener('click', onCancel);
      confirmModal.addEventListener('click', onBackdrop);
      document.addEventListener('keydown', onKeydown);
    });
  }

  const closeMessageModal = () => {
    messageModal.hidden = true;
  };

  messageClose.addEventListener('click', closeMessageModal);

  messageModal.addEventListener('click', event => {
    if (event.target === messageModal) closeMessageModal();
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !messageModal.hidden) {
      closeMessageModal();
    }
  });

  // Load series into the chapter form and series list
  async function loadSeries() {
    const res = await api('admin_series.php');
    const list = res.data || [];
    document.getElementById('totalSeries').textContent = list.length.toLocaleString();
    document.getElementById('seriesSelect').innerHTML = list
      .map(s =>
        `<option value="${Number(s.id)}">${escapeHtml(s.title)}</option>`
      )
      .join('');

    document.getElementById('seriesList').innerHTML = list.length
      ? list.map(s => `
          <div class="d-flex flex-wrap justify-content-between
                      align-items-center gap-2 border-bottom py-3">
            <span class="text-white">
              ${escapeHtml(s.title)}
              <small class="text-muted-nv">
                (${escapeHtml(s.type)})
              </small>
            </span>

            <a class="btn btn-sm btn-outline-light"
               href="series.php?slug=${encodeURIComponent(s.slug || '')}">
              View
            </a>
          </div>
        `).join('')
      : '<p class="text-muted-nv">Nothing yet.</p>';
  }

  // Escape API values before inserting them into HTML
  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    })[char]);
  }
  
  // Load real statistics from the database
  async function loadOverviewStats() {
    const res = await api('admin_stats.php');
    const stats = res.data;

    document.getElementById('totalUsers').textContent =
      Number(stats.total_users).toLocaleString();

    document.getElementById('totalAuthors').textContent =
      Number(stats.total_authors).toLocaleString();

    document.getElementById('totalSeries').textContent =
      Number(stats.total_series).toLocaleString();

    document.getElementById('totalChapters').textContent =
      Number(stats.total_chapters).toLocaleString();

    document.getElementById('pendingApplications').textContent =
      Number(stats.pending_applications).toLocaleString();
  }

  // Load author applications
  async function loadAuthorApplications() {
    const container = document.getElementById('applicationList');
    if (!container) return;

    container.innerHTML =
      '<p class="text-muted-nv">Loading applications...</p>';

    try {
      const res = await api('admin_author_applications.php');
      const applications = res.data || [];
      const pendingCount = applications.filter(app => String(app.status || 'pending').toLowerCase() === 'pending').length;

document.getElementById('pendingApplications').textContent =
  pendingCount.toLocaleString();
      if (!applications.length) {
        container.innerHTML =
          '<p class="text-muted-nv mb-0">No author applications yet.</p>';
        return;
      }

      container.innerHTML = applications.map(app => {
        const status = String(app.status || 'pending').toLowerCase();

        const badgeClass = {
          pending: 'bg-warning text-dark',
          approved: 'bg-success',
          rejected: 'bg-danger'
        }[status] || 'bg-secondary';

        const sampleUrl = String(app.sample_work_url || '');
        let sampleLink =
          '<span class="text-muted-nv">Not provided</span>';

        try {
          const parsedUrl = new URL(sampleUrl);

          if (['http:', 'https:'].includes(parsedUrl.protocol)) {
            sampleLink = `
              <a href="${escapeHtml(parsedUrl.href)}"
                 target="_blank"
                 rel="noopener noreferrer">View sample work</a>`;
          }
        } catch (_) {
          // Invalid sample URLs are not made clickable.
        }

        const isPending = status === 'pending';

        return `
          <div class="border border-secondary rounded p-3 mb-3">
            <div class="d-flex flex-wrap justify-content-between
                        gap-2 mb-2">
              <div>
                <h6 class="text-white mb-1">
                  ${escapeHtml(app.display_name)}
                </h6>
                <small class="text-muted-nv">
                  @${escapeHtml(app.username)} ·
                  ${escapeHtml(app.email)}
                </small>
              </div>

              <span class="badge ${badgeClass}">
                ${escapeHtml(status.toUpperCase())}
              </span>
            </div>

            <p class="text-white mb-2"
               style="white-space:pre-wrap">${escapeHtml(app.bio)}</p>

            <p class="mb-2">
              <strong class="text-white">Sample work:</strong>
              ${sampleLink}
            </p>

            <small class="text-muted-nv d-block mb-3">
              Submitted: ${escapeHtml(app.created_at)}
            </small>

            ${app.review_note ? `
              <p class="text-muted-nv mb-3">
                <strong>Review note:</strong>
                ${escapeHtml(app.review_note)}
              </p>
            ` : ''}

            ${isPending ? `
              <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success btn-sm"
                        data-review="approve"
                        data-id="${Number(app.id)}">
                  <i class="bi bi-check-circle"></i> Approve
                </button>

                <button class="btn btn-outline-danger btn-sm"
                        data-review="reject"
                        data-id="${Number(app.id)}">
                  <i class="bi bi-x-circle"></i> Reject
                </button>
              </div>
            ` : ''}
          </div>
        `;
      }).join('');
    } catch (err) {
      container.innerHTML =
        `<p class="text-danger">${escapeHtml(err.message)}</p>`;
    }
  }

  // Refresh author applications
  document.getElementById('refreshApplications')
    .addEventListener('click', loadAuthorApplications);

  // Approve or reject an application
  document.getElementById('applicationList')
    .addEventListener('click', async event => {
      const button = event.target.closest('button[data-review]');
      if (!button) return;

      const action = button.dataset.review;
      const id = Number(button.dataset.id);

      if (
        !['approve', 'reject'].includes(action) ||
        !Number.isInteger(id) ||
        id < 1
      ) return;

      const confirmed = await askConfirmation(
        action === 'approve'
          ? 'Approve this application and grant Author access?'
          : 'Reject this author application?',
        action === 'approve' ? 'Approve' : 'Reject'
      );

      if (!confirmed) return;

      button.disabled = true;

      try {
        await api('admin_author_applications.php', {
          method: 'POST',
          body: JSON.stringify({
            id,
            action,
            review_note: ''
          })
        });

        show(
          action === 'approve'
            ? 'The application has been approved. The user now has Author access.'
            : 'The author application has been rejected.',
          true,
          action === 'approve'
            ? 'Author Approved!'
            : 'Application Rejected'
        );

        await loadAuthorApplications();
      } catch (err) {
        show(err.message || 'Unable to review this application.', false);
      } finally {
        button.disabled = false;
      }
    });

  // Add Series
  document.getElementById('seriesForm')
    .addEventListener('submit', async event => {
      event.preventDefault();

      const form = event.currentTarget;
      const data = Object.fromEntries(new FormData(form));

      try {
        const res = await api('admin_series.php', {
          method: 'POST',
          body: JSON.stringify(data)
        });

        show(`Series created! (id ${res.data.id})`);
        form.reset();
        await loadSeries();
      } catch (err) {
        show(err.message, false);
      }
    });

  // Add Chapter
  document.getElementById('chapterForm')
    .addEventListener('submit', async event => {
      event.preventDefault();

      const form = event.currentTarget;
      const data = Object.fromEntries(new FormData(form));
      data.is_free = document.getElementById('isFree').checked;

      try {
        const res = await api('admin_chapters.php', {
          method: 'POST',
          body: JSON.stringify(data)
        });

        show(`Chapter saved with ${res.data.pages} pages! 🎉`);
        form.reset();
      } catch (err) {
        show(err.message, false);
      }
    });

  
  // Highlight the sidebar link for the current section
  const adminSections = [
    'overview',
    'add-series',
    'add-chapter',
    'applications',
    'your-series'
  ];

  const adminLinks = document.querySelectorAll(
    '.nv-admin-link[href^="#"]'
  );

  function setActiveAdminLink(sectionId) {
    adminLinks.forEach(link => {
      link.classList.toggle(
        'active',
        link.getAttribute('href') === '#' + sectionId
      );
    });
  }

  adminLinks.forEach(link => {
    link.addEventListener('click', () => {
      const sectionId = link.getAttribute('href').slice(1);
      setActiveAdminLink(sectionId);
    });
  });

  document.querySelectorAll('.nv-quick-card[href^="#"]')
    .forEach(link => {
      link.addEventListener('click', () => {
        setActiveAdminLink(link.getAttribute('href').slice(1));
      });
    });


  
  // Initialize dashboard
  loadSeries().catch(err => {
    show(err.message + ' — are you logged in as an admin?', false);
  });

  loadAuthorApplications();

  loadOverviewStats().catch(err => {
    console.error('Could not load dashboard statistics:', err);
  });

</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

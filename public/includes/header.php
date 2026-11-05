<?php
require_once __DIR__ . '/init.php';

$nvRole   = $nv_user['role'] ?? '';
$isAdmin  = $nvRole === 'admin' || !empty($nv_user['is_admin']);
$isAuthor = $nvRole === 'author';

$BASE = '/public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . ' — KomikByte' : 'KomikByte' ?></title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $BASE ?>/css/style.css?v=22">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <style>
    .nv-coins-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: linear-gradient(90deg, #f59e0b, #d97706);
      color: #111 !important;
      border: none;
      border-radius: 999px;
      padding: 0.35rem 0.85rem;
      font-weight: 700;
      text-decoration: none;
      white-space: nowrap;
    }
    .nv-coins-btn:hover {
      filter: brightness(1.08);
      color: #111 !important;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top nv-navbar">
  <div class="container">
    <a class="navbar-brand nv-brand" href="<?= $BASE ?>/index.php">
      <i class="bi bi-book-half"></i>
      <span>KOMIKBYTE</span>
    </a>
    <button
      class="navbar-toggler nv-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#nav"
      aria-controls="nav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto nv-nav-links">
        <li class="nav-item">
          <a class="nav-link" href="<?= $BASE ?>/browse.php?type=manga">Manga</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $BASE ?>/browse.php?type=manhwa">Manhwa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $BASE ?>/browse.php?type=manhua">Manhua</a>
        </li>
      </ul>
      <form class="d-flex nv-search" onsubmit="goSearch(event)">
        <input
          class="form-control"
          type="search"
          id="navSearch"
          placeholder="Search KomikByte..."
          aria-label="Search KomikByte"
        >
        <button class="btn nv-search-btn" type="submit" aria-label="Search">
          <i class="bi bi-search"></i>
        </button>
      </form>
      <div class="d-flex gap-2 nv-account-actions align-items-center">
        <?php if ($nv_user): ?>

          <a class="btn nv-coins-btn" href="<?= $BASE ?>/shop.php" title="Buy coins">
            <i class="bi bi-coin"></i>
            <span id="navCoinBalance">…</span>
          </a>

          <?php if (!$isAdmin && !$isAuthor): ?>
            <a class="btn btn-outline-nv" href="<?= $BASE ?>/author/register.php">
              <i class="bi bi-pen me-1"></i>
              Apply to Become an Author
            </a>
          <?php endif; ?>

          <?php if ($isAuthor): ?>
            <a class="btn nv-dashboard-btn" href="<?= $BASE ?>/author/dashboard.php">
              <i class="bi bi-speedometer2"></i>
              <span>Dashboard</span>
            </a>
          <?php elseif ($isAdmin): ?>
            <a class="btn nv-dashboard-btn nv-admin-dashboard"
               href="<?= $BASE ?>/admin/dashboard.php"
               aria-label="Admin dashboard"
               title="Admin dashboard">
              <i class="bi bi-shield-lock"></i>
              <span>Dashboard</span>
            </a>
          <?php endif; ?>

          <a class="btn nv-profile-btn" href="<?= $BASE ?>/profile.php">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars($nv_user['username'] ?? 'Profile', ENT_QUOTES, 'UTF-8') ?>
          </a>

          <button
            class="btn nv-logout-btn"
            type="button"
            onclick="api('auth.php?action=logout').then(() => location.href='<?= $BASE ?>/index.php')"
          >
            <i class="bi bi-box-arrow-right me-1"></i>
            Logout
          </button>

        <?php else: ?>
          <a class="btn nv-login-btn" href="<?= $BASE ?>/login.php">
            <i class="bi bi-person me-1"></i>
            Login / Register
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
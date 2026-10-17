<?php require_once __DIR__ . '/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= isset($pageTitle) ? $pageTitle . ' — NovelVerse' : 'NovelVerse' ?></title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/novelverse/public/css/style.css?v=2">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-moon-stars-fill me-2"></i>NOVELVERSE</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="browse.php?type=manga">Manga</a></li>
        <li class="nav-item"><a class="nav-link" href="browse.php?type=manhwa">Manhwa</a></li>
        <li class="nav-item"><a class="nav-link" href="browse.php?type=manhua">Manhua</a></li>
        <li class="nav-item"><a class="nav-link" href="browse.php?type=novel">Light Novels</a></li>
      </ul>
      <form class="d-flex me-2" onsubmit="goSearch(event)">
        <input class="form-control me-2" type="search" id="navSearch" placeholder="Search NovelVerse...">
        <button class="btn btn-nv" type="submit"><i class="bi bi-search"></i></button>
      </form>
      <div class="d-flex gap-2">
          <?php if ($nv_user): ?>

          <a class="btn btn-outline-nv" href="profile.php">
              <i class="bi bi-person-circle me-1"></i>
              <?= htmlspecialchars($nv_user['username']) ?>
          </a>

          <?php if (($nv_user['role'] ?? '') === 'author'): ?>
              <a class="btn btn-nv" href="author/dashboard.php">
                  <i class="bi bi-pencil-square me-1"></i>
                  Author Dashboard
              </a>
          <?php endif; ?>

          <?php if ((int)($nv_user['is_admin'] ?? 0) === 1): ?>
              <a class="btn btn-nv" href="admin/dashboard.php">
                  <i class="bi bi-speedometer2 me-1"></i>
                  Admin Dashboard
              </a>
          <?php endif; ?>

          <button
              class="btn btn-outline-nv"
              onclick="api('auth.php?action=logout').then(() => location.reload())">
              Logout
          </button>

      <?php else: ?>

          <button
              class="btn btn-nv"
              data-bs-toggle="modal"
              data-bs-target="#authModal">
              Login / Register
          </button>

      <?php endif; ?>

      </div>
    </div>
  </div>
</nav>
<?php
require_once __DIR__ . '/init.php';

$nvRole = $nv_user['role'] ?? '';
$isAdmin = $nvRole === 'admin' || !empty($nv_user['is_admin']);
$isAuthor = $nvRole === 'author';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . ' — NovelVerse' : 'NovelVerse' ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/novelverse/public/css/style.css?v=2">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top nv-navbar">
    <div class="container">

        <a class="navbar-brand nv-brand" href="public/index.php">
            <i class="bi bi-moon-stars-fill"></i>
            <span>NOVELVERSE</span>
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
                    <a class="nav-link" href="browse.php?type=manga">Manga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="browse.php?type=manhwa">Manhwa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="browse.php?type=manhua">Manhua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="browse.php?type=novel">Light Novels</a>
                </li>
            </ul>

            <form class="d-flex nv-search" onsubmit="goSearch(event)">
                <input
                    class="form-control"
                    type="search"
                    id="navSearch"
                    placeholder="Search NovelVerse..."
                    aria-label="Search NovelVerse"
                >
                <button class="btn nv-search-btn" type="submit" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <div class="d-flex gap-2 nv-account-actions">
                    <?php if ($nv_user): ?>
                        <?php if (!$isAdmin && !$isAuthor): ?>
                            <a class="btn btn-outline-nv"
                            href="/novelverse/public/author/register.php">
                                <i class="bi bi-pen me-1"></i>
                                Apply to Become an Author
                            </a>
                        <?php endif; ?>

                        <?php if ($isAuthor): ?>
                        <a class="btn nv-dashboard-btn" href="author/dashboard.php">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    <?php elseif ($isAdmin): ?> 
                        <a class="btn nv-dashboard-btn nv-admin-dashboard"
                        href="admin/dashboard.php"
                        aria-label="Admin dashboard"
                        title="Admin dashboard">
                            <i class="bi bi-shield-lock"></i>
                            <span>Dashboard</span>
                        </a>
                    <?php endif; ?>
                    <a class="btn nv-profile-btn" href="profile.php">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($nv_user['username'] ?? 'Profile', ENT_QUOTES, 'UTF-8') ?>
                    </a>

                    <button
                        class="btn nv-logout-btn"
                        type="button"
                        onclick="api('auth.php?action=logout').then(() => location.reload())"
                    >
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Logout
                    </button>

                <?php else: ?>

                    <button
                        class="btn nv-login-btn"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#authModal"
                    >
                        <i class="bi bi-person me-1"></i>
                        Login / Register
                    </button>

                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>


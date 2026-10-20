
<?php
require_once __DIR__ . '/../../backend/db.php';

// This page returns HTML, not JSON.
header('Content-Type: text/html; charset=UTF-8');

$user = current_user();

if (!$user || ($user['role'] ?? '') !== 'author') {
    http_response_code($user ? 403 : 401);
    exit(
        $user
            ? 'Access denied. An approved author account is required.'
            : 'Please log in with your approved author account.'
    );
}

$username = htmlspecialchars(
    $user['username'] ?? 'Author',
    ENT_QUOTES,
    'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Studio | NovelVerse</title>

    <style>
        :root {
            color-scheme: dark;
            --bg: #0c0d12;
            --panel: #151720;
            --panel-light: #1c1f2b;
            --border: #292d3a;
            --text: #f5f5fa;
            --muted: #969bad;
            --accent: #a78bfa;
            --accent-hover: #9170f2;
            --success: #4ade80;
            --danger: #fb7185;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        button, input, select, textarea {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        .layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 245px minmax(0, 1fr);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 245px;
            height: 100vh;
            overflow-y: auto;
            padding: 28px 18px;
            background: #101117;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 28px;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 0 8px;
        }

        .brand-icon {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: var(--accent);
            color: #171025;
            font-size: 22px;
            font-weight: 900;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 800;
        }

        .brand-sub {
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
        }

        .nav-label {
            margin: 0 10px 10px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            margin-bottom: 5px;
            padding: 12px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #b7bac9;
            text-align: left;
            text-decoration: none;
            transition: background .2s, color .2s;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #29223c;
            color: #d9ccff;
        }

        .sidebar-bottom {
            margin-top: auto;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 8px 0;
            border-top: 1px solid var(--border);
        }

        .avatar {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #33294d;
            color: #d9ccff;
            font-weight: 800;
        }

        .profile-name {
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 13px;
            font-weight: 700;
        }

        .profile-role {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        main {
            grid-column: 2;
            min-width: 0;
            padding: 30px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 34px;
        }

        .eyebrow {
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0;
            font-size: clamp(25px, 3vw, 34px);
            letter-spacing: -1px;
        }

        .muted {
            color: var(--muted);
            line-height: 1.6;
        }

        .button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--panel-light);
            color: var(--text);
            text-decoration: none;
            transition: .2s;
        }

        .button:hover {
            border-color: var(--accent);
            transform: translateY(-1px);
        }

        .button.primary {
            border-color: var(--accent);
            background: var(--accent);
            color: #171025;
            font-weight: 800;
        }

        .button.primary:hover {
            background: var(--accent-hover);
        }

        .button.danger {
            color: var(--danger);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .stat-card, .panel {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--panel);
        }

        .stat-card {
            padding: 22px;
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: var(--muted);
            font-size: 13px;
        }

        .stat-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: #29223c;
            color: #d9ccff;
            font-size: 19px;
        }

        .stat-number {
            margin-top: 17px;
            font-size: 30px;
            font-weight: 800;
        }

        .stat-caption {
            margin-top: 5px;
            color: var(--muted);
            font-size: 12px;
        }

        .panel {
            overflow: hidden;
            margin-bottom: 24px;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--border);
        }

        .panel-header h2 {
            margin: 0;
            font-size: 17px;
        }

        .panel-body {
            padding: 22px;
        }

        .series-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .series-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #11131b;
            transition: transform .2s, border-color .2s;
        }

        .series-card:hover {
            transform: translateY(-3px);
            border-color: #65518f;
        }

        .cover {
            display: grid;
            place-items: center;
            width: 100%;
            aspect-ratio: 3 / 4;
            background: #242332;
            color: var(--muted);
            font-size: 42px;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .series-info {
            padding: 15px;
        }

        .series-title {
            margin: 0 0 9px;
            overflow-wrap: anywhere;
            font-size: 15px;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 12px;
        }

        .tag {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 6px;
            background: #262332;
            color: #d0c0ff;
            font-size: 11px;
        }

        .tag.status {
            background: #193326;
            color: var(--success);
        }

        .series-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 13px;
        }

        .series-actions .button {
            flex: 1;
            min-height: 36px;
            padding: 8px;
            font-size: 12px;
        }

        .empty {
            padding: 42px 16px;
            color: var(--muted);
            text-align: center;
        }

        .empty-icon {
            margin-bottom: 12px;
            font-size: 35px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 17px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field label {
            color: #d4d6e2;
            font-size: 13px;
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            min-width: 0;
            padding: 12px 13px;
            border: 1px solid #353949;
            border-radius: 9px;
            outline: none;
            background: #10121a;
            color: var(--text);
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px #a78bfa20;
        }

        textarea {
            min-height: 115px;
            resize: vertical;
        }

        input[type="file"] {
            padding: 10px;
        }

        input[type="checkbox"] {
            width: auto;
            accent-color: var(--accent);
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #d4d6e2;
            font-size: 13px;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .notice {
            display: none;
            margin-bottom: 18px;
            padding: 13px 15px;
            border: 1px solid var(--border);
            border-radius: 10px;
            line-height: 1.5;
        }

        .notice.show {
            display: block;
        }

        .notice.success {
            border-color: #285a3b;
            background: #14251b;
            color: var(--success);
        }

        .notice.error {
            border-color: #63313d;
            background: #2a171d;
            color: #fda4af;
        }

        .chapter-list {
            display: grid;
            gap: 10px;
        }

        .chapter-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #11131b;
        }

        .chapter-name {
            font-size: 14px;
            font-weight: 700;
        }

        .chapter-meta {
            margin-top: 5px;
            color: var(--muted);
            font-size: 12px;
        }

        .hidden {
            display: none !important;
        }

        @media (max-width: 1100px) {
            .series-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 800px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                width: auto;
                height: auto;
                overflow-y: visible;
                padding: 15px;
                gap: 17px;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .sidebar nav {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .nav-label {
                width: 100%;
            }

            .nav-item {
                width: auto;
                margin: 0;
                padding: 9px 11px;
            }

            .sidebar-bottom {
                display: none;
            }

            main {
                padding: 22px 16px;
            }

            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .stats, .series-grid, .form-grid {
                grid-template-columns: 1fr;
            }

            .field.full {
                grid-column: auto;
            }

            .stat-card {
                padding: 17px;
            }

            .panel-body {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">N</div>
            <div>
                <div class="brand-name">NovelVerse</div>
                <div class="brand-sub">Author Studio</div>
            </div>
        </div>

        <nav>
            <div class="nav-label">Workspace</div>

            <a class="nav-item active" href="#overview">▦ Overview</a>
            <a class="nav-item" href="#series">▤ My Series</a>
            <a class="nav-item" href="#create-series">＋ Create Series</a>
            <a class="nav-item" href="#chapters">▧ Chapters</a>
        </nav>

        <div class="sidebar-bottom">
            <a class="nav-item" href="../index.php">← Back to NovelVerse</a>
            <div class="profile">
                <div class="avatar">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
                <div>
                    <div class="profile-name"><?= $username ?></div>
                    <div class="profile-role">Approved Author</div>
                </div>
            </div>
        </div>
    </aside>

    <main>
        <header class="topbar" id="overview">
            <div>
                <div class="eyebrow">Creator workspace</div>
                <h1>Welcome back, <?= $username ?>!</h1>
                <div class="muted">
                    Manage your stories, publish chapters, and grow your readership.
                </div>
            </div>

            <button class="button primary"
                    type="button"
                    onclick="document.querySelector('#create-series').scrollIntoView({behavior:'smooth'})">
                + New Series
            </button>
        </header>

        <div id="notice" class="notice" role="status" aria-live="polite"></div>

        <section class="stats">
            <article class="stat-card">
                <div class="stat-top">
                    <span>Total Series</span>
                    <span class="stat-icon">▤</span>
                </div>
                <div class="stat-number" id="seriesCount">—</div>
                <div class="stat-caption">Stories you manage</div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <span>Total Chapters</span>
                    <span class="stat-icon">▧</span>
                </div>
                <div class="stat-number" id="chapterCount">—</div>
                <div class="stat-caption">Across your series</div>
            </article>

            <article class="stat-card">
                <div class="stat-top">
                    <span>Total Views</span>
                    <span class="stat-icon">◉</span>
                </div>
                <div class="stat-number" id="viewsCount">—</div>
                <div class="stat-caption">Views reported by your series</div>
            </article>
        </section>

        <section class="panel" id="series">
            <div class="panel-header">
                <h2>My Series</h2>
                <button type="button" class="button" id="refreshButton">
                    ↻ Refresh
                </button>
            </div>
            <div class="panel-body">
                <div id="seriesGrid" class="series-grid">
                    <div class="empty">Loading your series…</div>
                </div>
            </div>
        </section>

        <section class="panel" id="create-series">
            <div class="panel-header">
                <h2>Create a Series</h2>
            </div>
            <div class="panel-body">
                <p class="muted">
                    Add your story details and an optional cover image.
                </p>

                <form id="seriesForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="field">
                            <label for="seriesTitle">Series title *</label>
                            <input id="seriesTitle" name="title"
                                   maxlength="255" required
                                   placeholder="Enter your story title">
                        </div>

                        <div class="field">
                            <label for="seriesType">Type *</label>
                            <select id="seriesType" name="type" required>
                                <option value="manga">Manga</option>
                                <option value="manhwa">Manhwa</option>
                                <option value="manhua">Manhua</option>
                                <option value="novel">Light Novel</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="seriesAuthor">Author name</label>
                            <input id="seriesAuthor" name="author"
                                   maxlength="120" placeholder="Pen name">
                        </div>

                        <div class="field">
                            <label for="seriesStatus">Status</label>
                            <select id="seriesStatus" name="status">
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="hiatus">Hiatus</option>
                            </select>
                        </div>

                        <div class="field full">
                            <label for="seriesDescription">Description</label>
                            <textarea id="seriesDescription"
                                      name="description"
                                      placeholder="Tell readers what your story is about…"></textarea>
                        </div>

                        <div class="field full">
                            <label for="seriesCover">Cover image (JPG, PNG, WebP, or GIF)</label>
                            <input id="seriesCover" name="cover"
                                   type="file"
                                   accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                        </div>

                        <div class="field hidden" id="priceField">
                            <label for="novelPrice">Novel price</label>
                            <input id="novelPrice" name="novel_price"
                                   type="number" min="0" step="0.01" value="0">
                        </div>

                        <div class="field">
                            <label for="freeChapters">Free chapters</label>
                            <input id="freeChapters" name="free_chapters"
                                   type="number" min="0" value="3">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button primary" type="submit">
                            Create Series
                        </button>
                        <button class="button" type="reset">Clear Form</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel" id="chapters">
            <div class="panel-header">
                <h2>Chapter Management</h2>
            </div>
            <div class="panel-body">
                <div class="field">
                    <label for="chapterSeries">Choose your series</label>
                    <select id="chapterSeries">
                        <option value="">Select a series…</option>
                    </select>
                </div>

                <div id="chapterSection" class="hidden" style="margin-top:24px">
                    <h3 id="selectedSeriesTitle">Publish a chapter</h3>

                    <form id="chapterForm" enctype="multipart/form-data">
                        <input type="hidden" name="series_id" id="chapterSeriesId">

                        <div class="form-grid">
                            <div class="field">
                                <label for="chapterNumber">Chapter number *</label>
                                <input id="chapterNumber" name="chapter_number"
                                       maxlength="20" required placeholder="1">
                            </div>

                            <div class="field">
                                <label for="chapterTitle">Chapter title</label>
                                <input id="chapterTitle" name="title"
                                       maxlength="255" placeholder="Optional title">
                            </div>

                            <div class="field full" id="mangaDexField">
                                <label for="mangaDexId">MangaDex chapter ID (optional)</label>
                                <input id="mangaDexId" name="md_chapter_id"
                                       placeholder="Paste a MangaDex chapter UUID">
                                <div class="muted">
                                    Use this for a MangaDex chapter instead of uploading pages.
                                </div>
                            </div>

                            <div class="field full hidden" id="novelContentField">
                                <label for="chapterContent">Chapter text *</label>
                                <textarea id="chapterContent" name="content"
                                          placeholder="Write or paste your chapter here…"
                                          style="min-height:260px"></textarea>
                            </div>

                            <div class="field full hidden" id="pageFilesField">
                                <label for="chapterPages">Chapter page images</label>
                                <input id="chapterPages" name="pages[]"
                                       type="file" multiple
                                       accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                                <div class="muted">
                                    Select multiple images in reading order.
                                </div>
                            </div>

                            <div class="field full">
                                <label class="checkbox-row">
                                    <input type="checkbox" name="is_free" value="1">
                                    Mark this chapter as free
                                </label>
                                <div class="muted">
                                    Your current upload API automatically determines
                                    free status from the series' free-chapter setting.
                                    This checkbox is informational and does not override that rule.
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="button primary" type="submit">
                                Publish Chapter
                            </button>
                        </div>
                    </form>

                    <div style="margin-top:30px">
                        <h3>Published Chapters</h3>
                        <div id="chapterList" class="chapter-list">
                            <div class="empty">Choose a series to view its chapters.</div>
                        </div>
                    </div>
                </div>

                <div id="noSeriesNotice" class="empty">
                    Create your first series to start publishing chapters.
                </div>
            </div>
        </section>
    </main>
</div>

<script>
(() => {
    'use strict';

    const API = '../../backend/api';
    const $ = (selector) => document.querySelector(selector);

    let seriesList = [];

    async function request(url, options = {}) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options
        });

        const raw = await response.text();
        let result;

        try {
            result = raw ? JSON.parse(raw) : {};
        } catch {
            throw new Error(
                `Server returned an unexpected response (HTTP ${response.status}).`
            );
        }

        if (!response.ok || result.error) {
            throw new Error(result.error || `Request failed (${response.status})`);
        }

        return result;
    }

    function notify(message, type = 'success') {
        const notice = $('#notice');
        notice.textContent = message;
        notice.className = `notice show ${type}`;
        notice.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[char]);
    }

    function coverUrl(path) {
        if (!path) return '';

        // Existing MangaDex covers are full HTTPS URLs.
        if (/^https?:\/\//i.test(path)) {
            try {
                const url = new URL(path);
                return ['http:', 'https:'].includes(url.protocol) ? url.href : '';
            } catch {
                return '';
            }
        }

        // Local uploads are stored relative to the public directory.
        const safePath = path.replace(/^\/+/, '');

        if (
            safePath.includes('..') ||
            !safePath.startsWith('uploads/covers/')
        ) {
            return '';
        }

        return '../' + safePath.split('/').map(encodeURIComponent).join('/');
    }

    function showCover(path, title) {
        const url = coverUrl(path);

        if (!url) {
            return '<div class="cover">▤</div>';
        }

        return `<div class="cover">
            <img src="${escapeHTML(url)}"
                 alt="${escapeHTML(title)} cover"
                 loading="lazy"
                 onerror="this.parentElement.textContent='▤';this.remove()">
        </div>`;
    }

    function seriesCard(series) {
        const id = Number(series.id);
        const title = escapeHTML(series.title);
        const type = escapeHTML(series.type || 'manga');
        const status = escapeHTML(series.status || 'ongoing');

        return `
            <article class="series-card">
                ${showCover(series.cover_url, series.title)}
                <div class="series-info">
                    <h3 class="series-title">${title}</h3>
                    <div class="meta">
                        <span class="tag">${type}</span>
                        <span class="tag status">${status}</span>
                    </div>
                    <div class="muted">
                        ${Number(series.views || 0).toLocaleString()} views
                    </div>
                    <div class="series-actions">
                        <button class="button"
                                type="button"
                                data-manage="${id}">
                            Chapters
                        </button>
                        <a class="button"
                           href="../index.php"
                           title="Return to NovelVerse">
                            View site
                        </a>
                    </div>
                </div>
            </article>`;
    }

    async function loadSeries() {
        $('#seriesGrid').innerHTML =
            '<div class="empty">Loading your series…</div>';

        try {
            const result = await request(`${API}/author_series.php`);
            seriesList = Array.isArray(result.data) ? result.data : [];

            $('#seriesCount').textContent = seriesList.length;
            $('#viewsCount').textContent = seriesList.reduce(
                (sum, series) => sum + Number(series.views || 0), 0
            ).toLocaleString();

            const select = $('#chapterSeries');
            const previous = select.value;

            select.innerHTML = '<option value="">Select a series…</option>';

            for (const series of seriesList) {
                const option = document.createElement('option');
                option.value = series.id;
                option.textContent = series.title;
                select.appendChild(option);
            }

            if (seriesList.some(s => String(s.id) === previous)) {
                select.value = previous;
            }

            $('#seriesGrid').innerHTML = seriesList.length
                ? seriesList.map(seriesCard).join('')
                : `<div class="empty" style="grid-column:1/-1">
                    <div class="empty-icon">▤</div>
                    <h3>No series yet</h3>
                    <p>Create your first story to get started.</p>
                    <button class="button primary" type="button"
                        onclick="document.querySelector('#create-series').scrollIntoView({behavior:'smooth'})">
                        Create your first series
                    </button>
                   </div>`;

            $('#noSeriesNotice').classList.toggle(
                'hidden', seriesList.length > 0
            );
            $('#chapterSection').classList.toggle(
                'hidden', seriesList.length === 0
            );

            if (select.value) {
                await loadChapters();
            } else {
                $('#chapterCount').textContent = '0';
                $('#chapterList').innerHTML =
                    '<div class="empty">Select a series to view chapters.</div>';
            }
        } catch (error) {
            $('#seriesGrid').innerHTML =
                `<div class="empty">${escapeHTML(error.message)}</div>`;
            notify('Could not load your series: ' + error.message, 'error');
        }
    }

    async function loadChapters() {
        const seriesId = $('#chapterSeries').value;
        const series = seriesList.find(s => String(s.id) === seriesId);

        if (!seriesId || !series) {
            $('#chapterList').innerHTML =
                '<div class="empty">Choose a series first.</div>';
            return;
        }

        $('#chapterSeriesId').value = seriesId;
        $('#selectedSeriesTitle').textContent =
            `Publish to: ${series.title}`;

        const isNovel = Number(series.is_novel) === 1 ||
                        series.type === 'novel';

        $('#novelContentField').classList.toggle('hidden', !isNovel);
        $('#pageFilesField').classList.toggle('hidden', isNovel);
        $('#mangaDexField').classList.toggle('hidden', isNovel);

        $('#chapterContent').required = isNovel;
        $('#chapterPages').required = !isNovel;

        $('#chapterList').innerHTML =
            '<div class="empty">Loading chapters…</div>';

        try {
            const result = await request(
                `${API}/author_chapters.php?series_id=${encodeURIComponent(seriesId)}`
            );
            const chapters = Array.isArray(result.data) ? result.data : [];

            // Store the most recently loaded total for the dashboard stats.
            $('#chapterList').dataset.count = String(chapters.length);

            $('#chapterList').innerHTML = chapters.length
                ? chapters.map(chapter => `
                    <div class="chapter-item">
                        <div>
                            <div class="chapter-name">
                                Chapter ${escapeHTML(chapter.chapter_number)}
                                ${chapter.title
                                    ? ' — ' + escapeHTML(chapter.title)
                                    : ''}
                            </div>
                            <div class="chapter-meta">
                                ${Number(chapter.views || 0).toLocaleString()} views
                                · ${Number(chapter.is_free) === 1 ? 'Free' : 'Locked'}
                            </div>
                        </div>
                        <button class="button danger"
                                type="button"
                                data-delete-chapter="${Number(chapter.id)}">
                            Delete
                        </button>
                    </div>
                `).join('')
                : '<div class="empty">No chapters published for this series yet.</div>';

            await updateChapterCount();
        } catch (error) {
            $('#chapterList').innerHTML =
                `<div class="empty">${escapeHTML(error.message)}</div>`;
        }
    }

    async function updateChapterCount() {
        let total = 0;

        for (const series of seriesList) {
            try {
                const result = await request(
                    `${API}/author_chapters.php?series_id=${encodeURIComponent(series.id)}`
                );
                total += Array.isArray(result.data) ? result.data.length : 0;
            } catch {
                // Don't expose inaccessible or failed requests as extra data.
            }
        }

        $('#chapterCount').textContent = total;
    }

    $('#seriesType').addEventListener('change', () => {
        $('#priceField').classList.toggle(
            'hidden', $('#seriesType').value !== 'novel'
        );
    });

    $('#seriesForm').addEventListener('reset', () => {
        setTimeout(() => {
            $('#priceField').classList.toggle(
                'hidden', $('#seriesType').value !== 'novel'
            );
        }, 0);
    });

    $('#seriesForm').addEventListener('submit', async event => {
        event.preventDefault();

        const button = event.submitter;
        if (button) {
            button.disabled = true;
            button.textContent = 'Creating…';
        }

        try {
            const formData = new FormData(event.currentTarget);

            const result = await request(`${API}/upload.php?action=series`, {
                method: 'POST',
                body: formData
            });

            event.currentTarget.reset();
            notify(
                `Series created successfully${result.data?.slug ? ': ' + result.data.slug : ''}.`
            );

            await loadSeries();
        } catch (error) {
            notify('Could not create series: ' + error.message, 'error');
        } finally {
            if (button) {
                button.disabled = false;
                button.textContent = 'Create Series';
            }
        }
    });

    $('#chapterSeries').addEventListener('change', loadChapters);

    $('#chapterForm').addEventListener('submit', async event => {
        event.preventDefault();

        const series = seriesList.find(
            s => String(s.id) === $('#chapterSeries').value
        );

        if (!series) {
            notify('Please select one of your series first.', 'error');
            return;
        }

        const button = event.submitter;
        if (button) {
            button.disabled = true;
            button.textContent = 'Publishing…';
        }

        try {
            const formData = new FormData(event.currentTarget);

            // This endpoint applies the series' free-chapter policy itself.
            formData.delete('is_free');

            const result = await request(`${API}/upload.php?action=chapter`, {
                method: 'POST',
                body: formData
            });

            event.currentTarget.reset();
            $('#chapterSeriesId').value = $('#chapterSeries').value;

            notify(
                `Chapter published successfully${result.data?.id ? ' (ID ' + result.data.id + ')' : ''}.`
            );

            await loadChapters();
        } catch (error) {
            notify('Could not publish chapter: ' + error.message, 'error');
        } finally {
            if (button) {
                button.disabled = false;
                button.textContent = 'Publish Chapter';
            }
        }
    });

    $('#seriesGrid').addEventListener('click', event => {
        const button = event.target.closest('[data-manage]');
        if (!button) return;

        $('#chapterSeries').value = button.dataset.manage;
        $('#chapters').scrollIntoView({ behavior: 'smooth' });
        loadChapters();
    });

    $('#chapterList').addEventListener('click', async event => {
        const button = event.target.closest('[data-delete-chapter]');
        if (!button) return;

        const id = Number(button.dataset.deleteChapter);

        if (!Number.isInteger(id) || id < 1) return;

        if (!window.confirm('Delete this chapter permanently?')) return;

        button.disabled = true;

        try {
            await request(`${API}/author_chapters.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'delete',
                    id
                })
            });

            notify('Chapter deleted.');
            await loadChapters();
        } catch (error) {
            notify('Could not delete chapter: ' + error.message, 'error');
            button.disabled = false;
        }
    });

    $('#refreshButton').addEventListener('click', loadSeries);

    loadSeries();
})();
</script>
</body>
</html>

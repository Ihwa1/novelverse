<?php
require_once __DIR__ . '/../db.php';
$pdo = db();
$action = $_GET['action'] ?? '';

// 🔒 Authors (and admins) only
$u = current_user();
if (!$u || (($u['role'] ?? '') !== 'author' && (int)($u['is_admin'] ?? 0) !== 1))
    json_out(['error' => 'Author account required'], 403);

$coverDir = __DIR__ . '/../../public/uploads/covers/';
$chapDir  = __DIR__ . '/../../public/uploads/chapters/';
foreach ([$coverDir, $chapDir] as $d) if (!is_dir($d)) mkdir($d, 0777, true);

function save_image(array $file, string $dir): string {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
        json_out(['error' => 'Only jpg / png / webp / gif images allowed'], 422);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;   // random name = no collisions
    move_uploaded_file($file['tmp_name'], $dir . $name);
    return $name;
}

// ── Create a series: POST upload.php?action=series (multipart form) ──
if ($action === 'series' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $type  = $_POST['type'] ?? '';
    if (!$title || !in_array($type, ['manga', 'manhwa', 'manhua', 'novel']))
        json_out(['error' => 'Title and a valid type are required'], 422);

    $isNovel = ($type === 'novel') ? 1 : 0;

    $cover = null;
    if (!empty($_FILES['cover']['tmp_name']))
        $cover = 'uploads/covers/' . save_image($_FILES['cover'], $coverDir);

    try {
        $st = $pdo->prepare('INSERT INTO series (title, slug, type, description, cover_url, author, status, is_novel, novel_price, free_chapters, created_by)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?)');
        $st->execute([
            $title, slugify($title), $type,
            trim($_POST['description'] ?? ''),
            $cover,
            trim($_POST['author'] ?? '') ?: $u['username'],
            in_array($_POST['status'] ?? '', ['ongoing', 'completed', 'hiatus']) ? $_POST['status'] : 'ongoing',
            $isNovel,
            $isNovel ? (float)($_POST['novel_price'] ?? 0) : 0,
            max(0, (int)($_POST['free_chapters'] ?? 3)),
            $u['id']
        ]);
        json_out(['data' => ['id' => $pdo->lastInsertId(), 'slug' => slugify($title)]], 201);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') json_out(['error' => 'A series with that title already exists'], 409);
        json_out(['error' => $e->getMessage()], 500);
    }
}

// ── Add a chapter: POST upload.php?action=chapter (multipart form) ──
if ($action === 'chapter' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare('SELECT * FROM series WHERE id = ?');
    $st->execute([(int)($_POST['series_id'] ?? 0)]);
    $series = $st->fetch();
    if (!$series) json_out(['error' => 'Series not found'], 404);
    if ((int)$series['created_by'] !== (int)$u['id'] && (int)($u['is_admin'] ?? 0) !== 1)
        json_out(['error' => 'This is not your series'], 403);

    $num   = trim($_POST['chapter_number'] ?? '');
    $title = trim($_POST['title'] ?? '');
    if ($num === '') json_out(['error' => 'Chapter number is required'], 422);

    // 🔄 MangaDex import support — reuse the existing external_id column
    $mdChapterId = trim($_POST['md_chapter_id'] ?? '');
    $externalId  = null;
    $pagesJson   = null;
    $content     = null;

    if ((int)$series['is_novel'] === 1) {
        // 📖 Light novel → text content
        $content = trim($_POST['content'] ?? '');
        if ($content === '') json_out(['error' => 'Novel chapters need text content'], 422);
    } elseif ($mdChapterId !== '') {
        // 🔄 MangaDex mode → no local files, reader fetches fresh pages via external_id
        // 🔄 CHANGED: cURL instead of file_get_contents (more reliable + real error messages)
        $ch = curl_init('https://api.mangadex.org/chapter/' . urlencode($mdChapterId));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'NovelVerse/1.0 (localhost dev)',
        ]);
        $check = curl_exec($ch);
        $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $cerr  = curl_error($ch);
        curl_close($ch);

        if ($check === false)
            json_out(['error' => 'Could not reach MangaDex API: ' . $cerr], 502);
        if ($http === 404)
            json_out(['error' => 'MangaDex chapter not found — check the ID'], 422);
        if ($http !== 200)
            json_out(['error' => "MangaDex API returned HTTP $http"], 502);

        $externalId = $mdChapterId;
    } else {
        // 🖼️ Comic → multiple page images
        if (empty($_FILES['pages']['name'][0]))
            json_out(['error' => 'Upload at least one page image OR provide a MangaDex chapter ID'], 422);
        $pages = [];
        foreach ($_FILES['pages']['name'] as $i => $n) {
            if ($_FILES['pages']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $pages[] = 'uploads/chapters/' . save_image(
                ['name' => $n, 'tmp_name' => $_FILES['pages']['tmp_name'][$i]],
                $chapDir
            );
        }
        if (!$pages) json_out(['error' => 'No valid page images uploaded'], 422);
        $pagesJson = json_encode($pages);   // order = upload order
    }

    // 🔒 Same paywall rule as imports: first free_chapters are free, rest locked
    $isFree = ((int)$num <= (int)$series['free_chapters']) ? 1 : 0;

    try {
        // 🔄 CHANGED: added external_id to the INSERT
        $st = $pdo->prepare('INSERT INTO chapters (series_id, chapter_number, title, is_free, pages_json, content, external_id)
                             VALUES (?,?,?,?,?,?,?)');
        $st->execute([$series['id'], $num, $title, $isFree, $pagesJson, $content, $externalId]);
        json_out(['data' => ['id' => $pdo->lastInsertId(), 'is_free' => (bool)$isFree]], 201);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') json_out(['error' => 'That chapter number already exists for this series'], 409);
        json_out(['error' => $e->getMessage()], 500);
    }
}

json_out(['error' => 'Unknown action'], 400);
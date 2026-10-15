<?php ini_set('display_errors', 1); error_reporting(E_ALL);
require_once __DIR__ . '/../db.php';
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

// Small helper to call the MangaDex API (same style as mangadex.php)
function md_get(string $path, array $params = []): array {
    $url = 'https://api.mangadex.org' . $path . ($params ? '?' . http_build_query($params) : '');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'NovelVerse/1.0',
        CURLOPT_HTTPHEADER => ['Accept: application/json']
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true) ?: [];
}

// ✅ FIX #2: paginate the ENTIRE feed so nothing gets truncated
function md_fetch_all_chapters(string $mangaId): array {
    $all = [];
    $limit = 100;
    for ($offset = 0; $offset < 20000; $offset += $limit) {
        $feed = md_get('/manga/' . $mangaId . '/feed', [
            'translatedLanguage' => ['en'],
            'order[chapter]'     => 'asc',
            'limit'              => $limit,
            'offset'             => $offset
        ]);
        $batch = $feed['data'] ?? [];
        $all = array_merge($all, $batch);
        if (count($batch) < $limit) break;   // last page reached
    }
    return $all;
}

// ✅ FIX #2b: pull the NEWEST chapters and insert any we're missing
function md_sync_latest($pdo, array $series): int {
    if (($series['external_source'] ?? '') !== 'mangadex' || empty($series['external_id'])) return 0;

    $freeCount = (int)($series['free_chapters'] ?? 3);
    $feed = md_get('/manga/' . $series['external_id'] . '/feed', [
        'translatedLanguage' => ['en'],
        'order[chapter]'     => 'desc',   // newest first — exactly what we were missing
        'limit'              => 100
    ]);

    $ins = $pdo->prepare('INSERT INTO chapters (series_id, chapter_number, title, is_free, external_id, release_date)
                          VALUES (?,?,?,?,?,?)');
    $added = 0;
    foreach ($feed['data'] ?? [] as $c) {
        $num = $c['attributes']['chapter'] ?? null;
        if ($num === null || $num === '') continue;
        try {
            $ins->execute([
                $series['id'], $num, $c['attributes']['title'] ?? '',
                ((float)$num <= $freeCount) ? 1 : 0,
                $c['id'],
                $c['attributes']['publishAt'] ?? date('Y-m-d H:i:s')
            ]);
            $added++;
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') throw $e;   // ignore dups, rethrow real errors
        }
    }
    return $added;
}

// ---------- Feature detection (works in ALL PDO error modes) ----------
$hasGenres = $hasRating = $hasProgress = false;
try {
    $r = $pdo->query('SELECT genres FROM series LIMIT 1');
    $hasGenres = ($r !== false);
} catch (Exception $e) {}
try {
    $r = $pdo->query('SELECT rating FROM series LIMIT 1');
    $hasRating = ($r !== false);
} catch (Exception $e) {}
try {
    $r = $pdo->query('SELECT 1 FROM reading_progress LIMIT 1');
    $hasProgress = ($r !== false);
} catch (Exception $e) {}

if ($method === 'GET') {

    // ============ SINGLE SERIES: ?slug=... (&sync=1 to refresh chapters) ============
    if (isset($_GET['slug'])) {
        $stmt = $pdo->prepare('SELECT * FROM series WHERE slug = ?');
        $stmt->execute([$_GET['slug']]);
        $series = $stmt->fetch();
        if (!$series) json_out(['error' => 'Not found'], 404);

        // ✅ Optional live sync — pulls newest MangaDex chapters on demand
        $synced = 0;
        if (isset($_GET['sync'])) {
            $synced = md_sync_latest($pdo, $series);
        }

        // ✅ FIX #1: DECIMAL cast — "10.5" sorts after "10", "2" sorts before "10"
                       $ch = $pdo->prepare('SELECT * FROM chapters WHERE series_id = ? ORDER BY CAST(chapter_number AS DECIMAL(10,2)) ASC');
        $ch->execute([$series['id']]);
        $series['chapters'] = $ch->fetchAll();

        $series['latest_chapter'] = null;
        if ($series['chapters']) {
            $series['latest_chapter'] = $series['chapters'][count($series['chapters']) - 1]['chapter_number'];
        }
        $series['synced_new_chapters'] = $synced;

        // does current user own this novel?
        $series['owned'] = false;
        if ($u = current_user()) {
            $p = $pdo->prepare("SELECT 1 FROM purchases WHERE user_id = ? AND series_id = ? AND status = 'paid'");
            $p->execute([$u['id'], $series['id']]);
            $series['owned'] = (bool)$p->fetch();
        }
        json_out(['data' => $series]);
    }

    // ============ BROWSE / DASHBOARD LIST ============
    $type    = $_GET['type']    ?? '';
    $q       = $_GET['q']       ?? '';
    $genre   = $_GET['genre']   ?? '';
    $sort    = $_GET['sort']    ?? 'latest';
    $range   = $_GET['range']   ?? 'all';
    $limit   = min(max((int)($_GET['limit'] ?? 60), 1), 100);

    // ---------- Sorting (JOIN must come BEFORE WHERE!) ----------
    $orderBy    = 's.created_at DESC';
    $joinSql    = '';
    $joinParams = [];

    if (($sort === 'trending' || $sort === 'popular') && $hasProgress) {
        $since = null;
        if ($range === 'week')  $since = date('Y-m-d H:i:s', strtotime('-7 days'));
        if ($range === 'month') $since = date('Y-m-d H:i:s', strtotime('-30 days'));

        if ($since) {
            $joinSql = ' LEFT JOIN (
                           SELECT series_id, COUNT(*) AS cnt
                           FROM reading_progress
                           WHERE updated_at >= ?
                           GROUP BY series_id
                         ) rp ON rp.series_id = s.id';
            $joinParams = [$since];
        } else {
            $joinSql = ' LEFT JOIN (
                           SELECT series_id, COUNT(*) AS cnt
                           FROM reading_progress
                           GROUP BY series_id
                         ) rp ON rp.series_id = s.id';
        }
        $orderBy = 'COALESCE(rp.cnt, 0) DESC, s.created_at DESC';
    }

    // ✅ FIX #1b: latest_chapter badge now uses DECIMAL too
    $sql    = "SELECT s.*,
                 (SELECT MAX(CAST(c.chapter_number AS DECIMAL(10,2))) FROM chapters c WHERE c.series_id = s.id)
                   AS latest_chapter
               FROM series s" . $joinSql . " WHERE 1";
    $params = $joinParams;

    if ($type !== '') {
        if ($type === 'comic')      { $sql .= " AND s.type != 'novel'"; }
        elseif ($type === 'novel')  { $sql .= " AND s.type = 'novel'"; }
        else                        { $sql .= ' AND s.type = ?'; $params[] = $type; }
    }

    if ($q !== '')    { $sql .= ' AND s.title LIKE ?';   $params[] = '%' . $q . '%'; }

    if ($genre !== '' && $hasGenres) {
        $sql .= ' AND s.genres LIKE ?'; $params[] = '%' . $genre . '%';
    }

    $sql .= " ORDER BY $orderBy LIMIT $limit";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$r) {
        $r['source'] = $r['external_source'] ?? null;
        if (!$hasRating) $r['rating'] = null;
    }
    unset($r);

    json_out(['data' => $rows]);
}

// POST — add a series (admin/import from MangaDex)
if ($method === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true);

    try {
        $stmt = $pdo->prepare('INSERT INTO series (title, slug, type, description, cover_url, author, status, is_novel, novel_price, free_chapters, external_source, external_id)
                               VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $in['title'], slugify($in['title']), $in['type'], $in['description'] ?? '',
            $in['cover'] ?? null, $in['author'] ?? null, $in['status'] ?? 'ongoing',
            ($in['type'] === 'novel') ? 1 : 0,
            $in['novel_price'] ?? 0, $in['free_chapters'] ?? 3,
            $in['external_source'] ?? null, $in['external_id'] ?? null
        ]);
        $seriesId = $pdo->lastInsertId();

    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            json_out([
                'status'  => 'exists',
                'message' => 'Already in NovelVerse!',
                'data'    => ['slug' => slugify($in['title'])]
            ], 200);
        } else {
            json_out(['error' => $e->getMessage()], 500);
        }
    }

    // ✅ Import chapters — now paginated, so LONG series come in COMPLETE
        // ✅ Import chapters from external sources
    $importedChapters = 0;
    $freeCount = (int)($in['free_chapters'] ?? 3);
    $source    = $in['external_source'] ?? '';
    $extId     = $in['external_id'] ?? '';

    // ── MangaDex ─────────────────────────────────────────────
    if ($source === 'mangadex' && $extId !== '') {
        $feed = md_fetch_all_chapters($extId);
        $ins  = $pdo->prepare('INSERT INTO chapters (series_id, chapter_number, title, is_free, external_id, release_date)
                               VALUES (?,?,?,?,?,?)');

        foreach ($feed as $c) {
            $num = $c['attributes']['chapter'] ?? null;
            if ($num === null || $num === '') continue;

            try {
                $ins->execute([
                    $seriesId,
                    $num,
                    $c['attributes']['title'] ?? '',
                    ((float)$num <= $freeCount) ? 1 : 0,
                    $c['id'],
                    $c['attributes']['publishAt'] ?? date('Y-m-d H:i:s')
                ]);
                $importedChapters++;
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') throw $e;
            }
        }
    }

              // ── Manhwa / Manhua ──────────────────────────────────────
    if ($source === 'manhwa' && $extId !== '') {
        // Use your local scraper
        $chUrl = 'http://localhost:3030/chapters/' . rawurlencode($extId);

        $ch = curl_init($chUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'NovelVerse/1.0',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);
        $chRes = curl_exec($ch);
        curl_close($ch);

        $allChapters = $chRes ? (json_decode($chRes, true)['data'] ?? []) : [];

        $ins = $pdo->prepare('INSERT INTO chapters (series_id, chapter_number, title, is_free, external_id, release_date)
                              VALUES (?,?,?,?,?,?)');

        foreach ($allChapters as $c) {
            if (!is_array($c)) continue;
            $num = $c['chapter'] ?? null;
            if ($num === null || $num === '') continue;

            try {
                $ins->execute([
                    $seriesId,
                    $num,
                    $c['title'] ?? '',
                    ((float)$num <= $freeCount) ? 1 : 0,
                    $c['id'] ?? '',
                    date('Y-m-d H:i:s')
                ]);
                $importedChapters++;
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') throw $e;
            }
        }
    }

        // ── Gutendex / Light Novel ───────────────────────────────
    if (($source === 'novel' || $source === 'gutendex') && $extId !== '') {
        // One "chapter" = full book text (fetched live when reading)
        $ins = $pdo->prepare('INSERT INTO chapters (series_id, chapter_number, title, is_free, external_id, release_date)
                              VALUES (?,?,?,?,?,?)');
        try {
            $ins->execute([
                $seriesId,
                '1',
                'Full text',
                1,          // public domain → free
                $extId,     // Gutendex book id
                date('Y-m-d H:i:s')
            ]);
            $importedChapters++;
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') throw $e;
        }
    }

    json_out([
        'data' => [
            'id' => $seriesId,
            'slug' => slugify($in['title']),
            'chapters_imported' => $importedChapters
        ]
    ], 201);
}

json_out(['error' => 'Method not allowed'], 405);
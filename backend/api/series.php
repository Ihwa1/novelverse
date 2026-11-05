<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../db.php';
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

function md_get(string $path, array $params = []): array {
    $url = 'https://api.mangadex.org' . $path . ($params ? '?' . http_build_query($params) : '');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'KomikByte/1.0',
        CURLOPT_HTTPHEADER => ['Accept: application/json']
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true) ?: [];
}

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
        if (count($batch) < $limit) break;
    }
    return $all;
}

function md_sync_latest($pdo, array $series): int {
    if (($series['external_source'] ?? '') !== 'mangadex' || empty($series['external_id'])) return 0;
    $feed = md_get('/manga/' . $series['external_id'] . '/feed', [
        'translatedLanguage' => ['en'],
        'order[chapter]'     => 'desc',
        'limit'              => 100
    ]);
    $ins = $pdo->prepare(
        'INSERT INTO chapters (series_id, chapter_number, title, is_free, coin_price, external_id, release_date)
         VALUES (?,?,?,?,?,?,?)'
    );
    $added = 0;
    foreach ($feed['data'] ?? [] as $c) {
        $num = $c['attributes']['chapter'] ?? null;
        if ($num === null || $num === '') continue;
        $n = (float)$num;
        $isFree = $n <= 20 ? 1 : 0;
        $coinPrice = $n <= 20 ? 0 : 25;
        try {
            $ins->execute([
                $series['id'],
                $num,
                $c['attributes']['title'] ?? '',
                $isFree,
                $coinPrice,
                $c['id'],
                $c['attributes']['publishAt'] ?? date('Y-m-d H:i:s')
            ]);
            $added++;
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') throw $e;
        }
    }
    return $added;
}

function apply_free_paid_rules(PDO $pdo, int $seriesId): void {
    $pdo->prepare("
        UPDATE chapters SET is_free = 1, coin_price = 0
        WHERE series_id = ? AND CAST(chapter_number AS DECIMAL(10,2)) <= 20
    ")->execute([$seriesId]);

    $pdo->prepare("
        UPDATE chapters SET is_free = 0, coin_price = 25
        WHERE series_id = ? AND CAST(chapter_number AS DECIMAL(10,2)) > 20
    ")->execute([$seriesId]);
}

$hasGenres = $hasRating = $hasProgress = false;
try { $r = $pdo->query('SELECT genres FROM series LIMIT 1'); $hasGenres = ($r !== false); } catch (Exception $e) {}
try { $r = $pdo->query('SELECT rating FROM series LIMIT 1'); $hasRating = ($r !== false); } catch (Exception $e) {}
try { $r = $pdo->query('SELECT 1 FROM reading_progress LIMIT 1'); $hasProgress = ($r !== false); } catch (Exception $e) {}

if ($method === 'GET') {

    // ============ SINGLE SERIES: ?slug=... ============
    if (isset($_GET['slug'])) {
        $stmt = $pdo->prepare('SELECT * FROM series WHERE slug = ?');
        $stmt->execute([$_GET['slug']]);
        $series = $stmt->fetch();
        if (!$series) json_out(['error' => 'Not found'], 404);

        $synced = 0;
        if (isset($_GET['sync'])) {
            $synced = md_sync_latest($pdo, $series);
            if ($synced > 0) {
                apply_free_paid_rules($pdo, (int)$series['id']);
            }
        }

        // Chapters + coin_price + unlocked for current user
        $user = current_user();
        $uid  = $user ? (int)$user['id'] : 0;

        if ($uid > 0) {
            $ch = $pdo->prepare("
                SELECT
                    c.id,
                    c.series_id,
                    c.chapter_number,
                    c.title,
                    c.is_free,
                    c.coin_price,
                    c.external_id,
                    c.pages_json,
                    c.content,
                    c.release_date,
                    CASE WHEN u.chapter_id IS NULL THEN 0 ELSE 1 END AS unlocked
                FROM chapters c
                LEFT JOIN chapter_unlocks u
                    ON u.chapter_id = c.id AND u.user_id = ?
                WHERE c.series_id = ?
                ORDER BY CAST(c.chapter_number AS DECIMAL(10,2)) ASC
            ");
            $ch->execute([$uid, $series['id']]);
        } else {
            $ch = $pdo->prepare("
                SELECT
                    c.id,
                    c.series_id,
                    c.chapter_number,
                    c.title,
                    c.is_free,
                    c.coin_price,
                    c.external_id,
                    c.pages_json,
                    c.content,
                    c.release_date,
                    0 AS unlocked
                FROM chapters c
                WHERE c.series_id = ?
                ORDER BY CAST(c.chapter_number AS DECIMAL(10,2)) ASC
            ");
            $ch->execute([$series['id']]);
        }

        $series['chapters'] = $ch->fetchAll();
        $series['latest_chapter'] = null;
        if ($series['chapters']) {
            $series['latest_chapter'] = $series['chapters'][count($series['chapters']) - 1]['chapter_number'];
        }
        $series['synced_new_chapters'] = $synced;

        $series['owned'] = false;
        if ($u = current_user()) {
            $p = $pdo->prepare("SELECT 1 FROM purchases WHERE user_id = ? AND series_id = ? AND status = 'paid'");
            $p->execute([$u['id'], $series['id']]);
            $series['owned'] = (bool)$p->fetch();
        }

        json_out(['data' => $series]);
    }

    // ============ BROWSE / LIST ============
    $type    = $_GET['type']    ?? '';
    $q       = $_GET['q']       ?? '';
    $genre   = $_GET['genre']   ?? '';
    $sort    = $_GET['sort']    ?? 'latest';
    $range   = $_GET['range']   ?? 'all';
    $limit   = min(max((int)($_GET['limit'] ?? 60), 1), 100);

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

    $sql = "SELECT s.*,
              (SELECT MAX(CAST(c.chapter_number AS DECIMAL(10,2))) FROM chapters c WHERE c.series_id = s.id)
                AS latest_chapter
            FROM series s" . $joinSql . " WHERE 1";
    $params = $joinParams;

    if ($type !== '') {
        if ($type === 'comic')     { $sql .= " AND s.type != 'novel'"; }
        elseif ($type === 'novel') { $sql .= " AND s.type = 'novel'"; }
        else                       { $sql .= ' AND s.type = ?'; $params[] = $type; }
    }
    if ($q !== '') { $sql .= ' AND s.title LIKE ?'; $params[] = '%' . $q . '%'; }
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

// POST — import series
if ($method === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO series (title, slug, type, description, cover_url, author, status, is_novel, novel_price, free_chapters, external_source, external_id)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $in['title'], slugify($in['title']), $in['type'], $in['description'] ?? '',
            $in['cover'] ?? null, $in['author'] ?? null, $in['status'] ?? 'ongoing',
            ($in['type'] === 'novel') ? 1 : 0,
            $in['novel_price'] ?? 0, $in['free_chapters'] ?? 20,
            $in['external_source'] ?? null, $in['external_id'] ?? null
        ]);
        $seriesId = (int)$pdo->lastInsertId();
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            json_out([
                'status'  => 'exists',
                'message' => 'Already in KomikByte!',
                'data'    => ['slug' => slugify($in['title'])]
            ], 200);
        } else {
            json_out(['error' => $e->getMessage()], 500);
        }
    }

    $importedChapters = 0;
    $source = $in['external_source'] ?? '';
    $extId  = $in['external_id'] ?? '';

    // MangaDex
    if ($source === 'mangadex' && $extId !== '') {
        $feed = md_fetch_all_chapters($extId);
        $ins  = $pdo->prepare(
            'INSERT INTO chapters (series_id, chapter_number, title, is_free, coin_price, external_id, release_date)
             VALUES (?,?,?,?,?,?,?)'
        );
        foreach ($feed as $c) {
            $num = $c['attributes']['chapter'] ?? null;
            if ($num === null || $num === '') continue;
            $n = (float)$num;
            try {
                $ins->execute([
                    $seriesId,
                    $num,
                    $c['attributes']['title'] ?? '',
                    $n <= 20 ? 1 : 0,
                    $n <= 20 ? 0 : 25,
                    $c['id'],
                    $c['attributes']['publishAt'] ?? date('Y-m-d H:i:s')
                ]);
                $importedChapters++;
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') throw $e;
            }
        }
    }

    // Manhwa scraper
    if ($source === 'manhwa' && $extId !== '') {
        $chUrl = 'http://localhost:3030/chapters/' . rawurlencode($extId);
        $ch = curl_init($chUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'KomikByte/1.0',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);
        $chRes = curl_exec($ch);
        curl_close($ch);
        $allChapters = $chRes ? (json_decode($chRes, true)['data'] ?? []) : [];
        $ins = $pdo->prepare(
            'INSERT INTO chapters (series_id, chapter_number, title, is_free, coin_price, external_id, release_date)
             VALUES (?,?,?,?,?,?,?)'
        );
        foreach ($allChapters as $c) {
            if (!is_array($c)) continue;
            $num = $c['chapter'] ?? null;
            if ($num === null || $num === '') continue;
            $n = (float)$num;
            try {
                $ins->execute([
                    $seriesId,
                    $num,
                    $c['title'] ?? '',
                    $n <= 20 ? 1 : 0,
                    $n <= 20 ? 0 : 25,
                    $c['id'] ?? '',
                    date('Y-m-d H:i:s')
                ]);
                $importedChapters++;
            } catch (PDOException $e) {
                if ($e->getCode() !== '23000') throw $e;
            }
        }
    }

    // Light novel / Gutendex
    if (($source === 'novel' || $source === 'gutendex') && $extId !== '') {
        $ins = $pdo->prepare(
            'INSERT INTO chapters (series_id, chapter_number, title, is_free, coin_price, external_id, release_date)
             VALUES (?,?,?,?,?,?,?)'
        );
        try {
            $ins->execute([
                $seriesId, '1', 'Full text', 1, 0, $extId, date('Y-m-d H:i:s')
            ]);
            $importedChapters++;
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') throw $e;
        }
    }

    apply_free_paid_rules($pdo, $seriesId);

    json_out([
        'data' => [
            'id' => $seriesId,
            'slug' => slugify($in['title']),
            'chapters_imported' => $importedChapters
        ]
    ], 201);
}

json_out(['error' => 'Method not allowed'], 405);
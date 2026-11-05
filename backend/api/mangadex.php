<?php
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';

function md_get(string $path, array $params = [], int $ttl = 3600): array {
    $url = 'https://api.mangadex.org' . $path;

    if ($params) {
        $qs = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $qs[] = urlencode($key) . '[]=' . urlencode((string)$v);
                }
            } else {
                $qs[] = urlencode($key) . '=' . urlencode((string)$value);
            }
        }
        $url .= '?' . implode('&', $qs);
    }

    $cacheDir = __DIR__ . '/../cache/mangadex';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    $cacheFile = $cacheDir . '/' . md5($url) . '.json';

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $ttl)) {
        $cachedData = file_get_contents($cacheFile);
        $decoded = json_decode($cachedData, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'KomikByte/1.0',
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);

    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($res, true);

    if ($httpCode === 200 && is_array($decoded)) {
        file_put_contents($cacheFile, $res);
        return $decoded;
    }

    if (file_exists($cacheFile)) {
        $staleData = json_decode(file_get_contents($cacheFile), true);
        if (is_array($staleData)) {
            return $staleData;
        }
    }

    return [];
}

switch ($action) {

    case 'search':
        $q = trim($_GET['q'] ?? '');
        $limit = min(30, (int)($_GET['limit'] ?? 20));

        $queryParams = [
            'limit' => $limit,
            'includes' => ['cover_art', 'author'],
            'contentRating' => ['safe', 'suggestive'],
            'order' => ['relevance' => 'desc'],
        ];

        if ($q !== '') {
            $queryParams['title'] = $q;
        } else {
            $queryParams['order'] = ['followedCount' => 'desc'];
        }

        $data = md_get('/manga', $queryParams, 3600);

        $out = [];
        foreach ($data['data'] ?? [] as $m) {
            $cover = '';
            foreach ($m['relationships'] ?? [] as $rel) {
                if (($rel['type'] ?? '') === 'cover_art') {
                    $file = $rel['attributes']['fileName'] ?? '';
                    if ($file !== '') {
                        $rawCover = 'https://uploads.mangadex.org/covers/' . $m['id'] . '/' . $file;
                        $cover = 'proxy.php?url=' . urlencode($rawCover);
                    }
                }
            }

            $titles = $m['attributes']['title'] ?? [];
            $title = isset($titles['en']) ? $titles['en'] : (count($titles) ? reset($titles) : 'Untitled');

            $descs = $m['attributes']['description'] ?? [];
            $desc = isset($descs['en']) ? $descs['en'] : (count($descs) ? reset($descs) : '');

            $lang = isset($m['attributes']['originalLanguage']) ? $m['attributes']['originalLanguage'] : 'ja';
            $type = 'manga';
            if ($lang === 'ko') $type = 'manhwa';
            if ($lang === 'zh') $type = 'manhua';

            $out[] = [
                'id'        => $m['id'],
                'title'     => $title,
                'cover'     => $cover,
                'cover_url' => $cover,
                'desc'      => $desc,
                'status'    => isset($m['attributes']['status']) ? $m['attributes']['status'] : 'ongoing',
                'type'      => $type,
                'author'    => '',
                'tags'      => [],
            ];
        }
        json_out(['data' => $out]);
        break;

    case 'chapters':
        $mangaId = trim($_GET['id'] ?? '');

        if ($mangaId === '') {
            json_out(['error' => 'Missing id'], 400);
        }

        $data = md_get('/manga/' . rawurlencode($mangaId) . '/feed', [
            'translatedLanguage' => ['en'],
            'order' => ['chapter' => 'asc'],
            'contentRating' => ['safe', 'suggestive'],
            'limit' => 500,
        ], 1800);

        $chapters = [];
        foreach ($data['data'] ?? [] as $c) {
            $pages = (int)($c['attributes']['pages'] ?? 0);
            $externalUrl = isset($c['attributes']['externalUrl']) ? $c['attributes']['externalUrl'] : null;

            if ($pages === 0 && $externalUrl !== null) {
                continue;
            }

            $chapters[] = [
                'id'           => $c['id'],
                'chapter'      => isset($c['attributes']['chapter']) ? $c['attributes']['chapter'] : null,
                'title'        => isset($c['attributes']['title']) ? $c['attributes']['title'] : '',
                'pages'        => $pages,
                'external_url' => $externalUrl,
                'source'       => 'mangadex'
            ];
        }

        json_out(['data' => $chapters]);
        break;

    case 'pages':
        $chapterId = trim($_GET['chapterId'] ?? '');
        if ($chapterId === '') {
            json_out(['error' => 'Missing chapterId'], 400);
        }

        $data = md_get('/at-home/server/' . rawurlencode($chapterId), [], 600);
        $base = isset($data['baseUrl']) ? $data['baseUrl'] : '';
        $hash = isset($data['chapter']['hash']) ? $data['chapter']['hash'] : '';
        $files = isset($data['chapter']['data']) ? $data['chapter']['data'] : [];

        if (!$base || !$hash || empty($files)) {
            json_out(['error' => 'Chapter images unavailable or rate limited'], 404);
        }

        $pages = [];
        foreach ($files as $p) {
            $pages[] = $base . '/data/' . $hash . '/' . $p;
        }

        json_out(['data' => [
            'base'  => '',
            'hash'  => '',
            'pages' => $pages
        ]]);
        break;

    default:
        json_out(['error' => 'Unknown action. Use: search | chapters | pages'], 400);
}
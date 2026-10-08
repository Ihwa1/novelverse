<?php
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';

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

switch ($action) {

    // Search MangaDex: ?action=search&q=solo+leveling&limit=20
    case 'search':
        $q = $_GET['q'] ?? '';
        $limit = min(30, (int)($_GET['limit'] ?? 20));
        $data = md_get('/manga', [
            'title' => $q, 'limit' => $limit,
            'includes' => ['cover_art', 'author'],
            'contentRating' => ['safe', 'suggestive'],
            'order[relevance]' => 'desc'
        ]);
        $out = [];  
        foreach ($data['data'] ?? [] as $m) {
            $cover = '';
            foreach ($m['relationships'] as $rel) {
                if ($rel['type'] === 'cover_art') {
                    $cover = 'https://uploads.mangadex.org/covers/' . $m['id'] . '/' . ($rel['attributes']['fileName'] ?? '');
                }
            }
            $out[] = [
                'id'        => $m['id'],
                'title'     => $m['attributes']['title']['en']
                            ?? reset($m['attributes']['title']) ?? 'Untitled',
                'cover'     => $cover,
                'desc'      => $m['attributes']['description']['en'] ?? '',
                'status'    => $m['attributes']['status'],
                // MangaDex gives originalLanguage: ja= manga, ko= manhwa, zh= manhua
                'type'      => match ($m['attributes']['originalLanguage'] ?? 'ja') {
                    'ko' => 'manhwa', 'zh' => 'manhua', default => 'manga'
                },
                'author'    => '',
                'tags'      => array_column($m['attributes']['tags'] ?? [], 'name')
            ];
        }
        json_out(['data' => $out]);

    // All chapters of a MangaDex title: ?action=chapters&id=UUID
    case 'chapters':
        $data = md_get('/manga/' . ($_GET['id'] ?? '') . '/feed', [
            'translatedLanguage' => ['en'],
            'order[chapter]' => 'asc',
            'limit' => 500,
            'includes' => []
        ]);
        $chapters = [];
        foreach ($data['data'] ?? [] as $c) {
            $chapters[] = [
                'id'      => $c['id'],
                'chapter' => $c['attributes']['chapter'],
                'title'   => $c['attributes']['title'],
                'pages'   => $c['attributes']['pages'],
                'external_url' => $c['attributes']['externalUrl']
            ];
        }
        json_out(['data' => $chapters]);

        // Page images of a chapter: ?action=pages&chapterId=UUID
    case 'pages':
        $data = md_get('/at-home/server/' . ($_GET['chapterId'] ?? ''), []);
        json_out(['data' => [
            'base'  => $data['baseUrl'] ?? '',
            'hash'  => $data['chapter']['hash'] ?? '',
            'pages' => array_map(fn($p) => ($data['baseUrl'] ?? '') . '/data/' . ($data['chapter']['hash'] ?? '') . '/' . $p,
                                 $data['chapter']['data'] ?? [])
        ]]);
        break;

    default:
        json_out(['error' => 'Unknown action'], 400);
}
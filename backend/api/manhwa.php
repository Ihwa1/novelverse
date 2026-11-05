<?php
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';

function anilist_query(string $query, array $vars = []): array {
    $ch = curl_init('https://graphql.anilist.co');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_USERAGENT      => 'KomikByte/1.0',
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_POSTFIELDS     => json_encode(['query' => $query, 'variables' => $vars]),
        CURLOPT_TIMEOUT        => 15,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res ? (json_decode($res, true) ?: []) : [];
}

switch ($action) {

    case 'search':
        $q = trim($_GET['q'] ?? '');
        if ($q === '') json_out(['error' => 'Missing ?q='], 400);

        $data = anilist_query('
          query ($search: String) {
            Page(page: 1, perPage: 20) {
              media(search: $search, type: MANGA, sort: SEARCH_MATCH, isAdult: false) {
                id
                title { romaji english }
                coverImage { large }
                description(asHtml: false)
                status
                genres
                countryOfOrigin
              }
            }
          }
        ', ['search' => $q]);

        $media = $data['data']['Page']['media'] ?? [];
        $out = [];
        foreach ($media as $m) {
            $cover = $m['coverImage']['large'] ?? null;
            $out[] = [
                'id'          => $m['id'],
                'title'       => $m['title']['english'] ?: ($m['title']['romaji'] ?? 'Unknown'),
                'cover'       => $cover,
                'cover_url'   => $cover,
                'description' => trim(strip_tags($m['description'] ?? '')),
                'desc'        => trim(strip_tags($m['description'] ?? '')),
                'status'      => strtolower($m['status'] ?? 'ongoing'),
                'type'        => (($m['countryOfOrigin'] ?? '') === 'KR') ? 'manhwa' : 'manga',
                'genres'      => $m['genres'] ?? [],
                'external_source' => 'anilist',
            ];
        }
        json_out(['data' => $out]);
        break;

    case 'detail':
    case 'chapters':
    case 'pages':
        // No chapter scraper on InfinityFree
        json_out([
            'error' => 'Chapter reading via scraper is not available on this host. Use MangaDex imports for chapters.'
        ], 501);
        break;

    default:
        json_out(['error' => 'Unknown action. Use: search'], 400);
}
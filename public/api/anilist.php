<?php
header('Content-Type: application/json');

if (!function_exists('json_out')) {
    function json_out($data, int $code = 200): void {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}

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
    $err = curl_error($ch);
    curl_close($ch);
    if ($res === false) json_out(['error' => 'AniList unreachable: ' . $err], 502);
    return json_decode($res, true) ?: [];
}

$action = $_GET['action'] ?? 'trending';

$FIELDS = '
  id
  title { romaji english native }
  coverImage { extraLarge large color }
  bannerImage
  description(asHtml: false)
  averageScore
  popularity
  genres
  status
  countryOfOrigin
  siteUrl
';

switch ($action) {
  case 'trending':
    $data = anilist_query('
      query ($page: Int) {
        Page(page: $page, perPage: 20) {
          pageInfo { total currentPage hasNextPage }
          media(countryOfOrigin: KR, type: MANGA, sort: TRENDING_DESC, isAdult: false) {
            ' . $FIELDS . '
          }
        }
      }', ['page' => (int)($_GET['page'] ?? 1)]);
    break;

  case 'popular':
    $data = anilist_query('
      query ($page: Int) {
        Page(page: $page, perPage: 20) {
          pageInfo { total currentPage hasNextPage }
          media(countryOfOrigin: KR, type: MANGA, sort: POPULARITY_DESC, isAdult: false) {
            ' . $FIELDS . '
          }
        }
      }', ['page' => (int)($_GET['page'] ?? 1)]);
    break;

  case 'search':
    $q = trim($_GET['q'] ?? '');
    if ($q === '') json_out(['error' => 'Missing ?q='], 400);
    $data = anilist_query('
      query ($search: String, $page: Int) {
        Page(page: $page, perPage: 20) {
          pageInfo { total currentPage hasNextPage }
          media(search: $search, type: MANGA, sort: SEARCH_MATCH, isAdult: false) {
            ' . $FIELDS . '
          }
        }
      }', ['search' => $q, 'page' => (int)($_GET['page'] ?? 1)]);
    break;

  default:
    json_out(['error' => 'Unknown action. Use: trending | popular | search'], 400);
}

if (isset($data['errors'])) {
    json_out(['anilist_errors' => $data['errors']], 502);
}

$media = $data['data']['Page']['media'] ?? [];

$out = array_map(function ($m) {
    $cover = $m['coverImage']['large'] ?? ($m['coverImage']['extraLarge'] ?? null);
    return [
        'id'          => $m['id'],
        'anilist_id'  => $m['id'],
        'title'       => $m['title']['english'] ?: ($m['title']['romaji'] ?? 'Unknown'),
        'native'      => $m['title']['native'] ?? '',
        'cover'       => $cover,
        'cover_url'   => $cover,
        'banner'      => $m['bannerImage'] ?? null,
        'score'       => $m['averageScore'],
        'popularity'  => $m['popularity'],
        'genres'      => $m['genres'] ?? [],
        'status'      => strtolower($m['status'] ?? 'ongoing'),
        'type'        => 'manhwa',
        'desc'        => trim(strip_tags($m['description'] ?? '')),
        'description' => trim(strip_tags($m['description'] ?? '')),
        'url'         => $m['siteUrl'] ?? null,
        '_source'     => 'anilist',
    ];
}, $media);

json_out([
    'data' => $out,
    'page' => $data['data']['Page']['pageInfo'] ?? null,
]);
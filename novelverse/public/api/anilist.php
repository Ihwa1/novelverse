<?php
// AniList proxy — metadata only (titles, covers, scores, descriptions)
// No content is hosted; links go to your browse/search flow

header('Content-Type: application/json');

// Fallback: define json_out if it's not already available from config
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
        CURLOPT_USERAGENT      => 'NovelVerse/1.0 (localhost dev)',
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_POSTFIELDS     => json_encode(['query' => $query, 'variables' => $vars]),
        CURLOPT_TIMEOUT        => 10,
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($res === false) json_out(['error' => 'AniList unreachable: ' . $err], 502);
    return json_decode($res, true);
}

$action = $_GET['action'] ?? 'trending';

// Base fields we want for every media item
const FIELDS = '
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

  // 🔥 Trending manhwa (Korean origin)
  case 'trending':
    $data = anilist_query('
      query ($page: Int) {
        Page(page: $page, perPage: 20) {
          pageInfo { total currentPage hasNextPage }
          media(countryOfOrigin: KR, type: MANGA, sort: TRENDING_DESC, isAdult: false) {
            ' . FIELDS . '
          }
        }
      }', ['page' => (int)($_GET['page'] ?? 1)]);
    break;

  // 🏆 All-time popular manhwa
  case 'popular':
    $data = anilist_query('
      query ($page: Int) {
        Page(page: $page, perPage: 20) {
          pageInfo { total currentPage hasNextPage }
          media(countryOfOrigin: KR, type: MANGA, sort: POPULARITY_DESC, isAdult: false) {
            ' . FIELDS . '
          }
        }
      }', ['page' => (int)($_GET['page'] ?? 1)]);
    break;

  // 🔍 Search (works for manga AND manhwa)
  case 'search':
    $q = trim($_GET['q'] ?? '');
    if ($q === '') json_out(['error' => 'Missing ?q='], 400);
    $data = anilist_query('
      query ($search: String, $page: Int) {
        Page(page: $page, perPage: 20) {
          pageInfo { total currentPage hasNextPage }
          media(search: $search, type: MANGA, sort: SEARCH_MATCH, isAdult: false) {
            ' . FIELDS . '
          }
        }
      }', ['search' => $q, 'page' => (int)($_GET['page'] ?? 1)]);
    break;

  default:
    json_out(['error' => 'Unknown action. Use: trending | popular | search'], 400);
}

// 🐛 If AniList returned GraphQL errors, show them instead of empty data
if (isset($data['errors'])) {
    json_out(['anilist_errors' => $data['errors']], 502);
}

$media = $data['data']['Page']['media'] ?? [];
$out = array_map(fn($m) => [
    'anilist_id' => $m['id'],
    'title'      => $m['title']['english'] ?: $m['title']['romaji'],
    'native'     => $m['title']['native'],
    'cover'      => $m['coverImage']['large'] ?? null,
    'banner'     => $m['bannerImage'] ?? null,
    'score'      => $m['averageScore'],
    'popularity' => $m['popularity'],
    'genres'     => $m['genres'],
    'status'     => $m['status'],
    'desc'       => trim(strip_tags($m['description'] ?? '')),
    'url'        => $m['siteUrl'],
], $media);

json_out([
    'data'  => $out,
    'page'  => $data['data']['Page']['pageInfo'] ?? null,
]);
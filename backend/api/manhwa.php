<?php
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';
define('MANHWA_BASE', 'http://localhost:3030');

function mh_get(string $path, array $params = []): array {
    $url = rtrim(MANHWA_BASE, '/') . $path . ($params ? '?' . http_build_query($params) : '');
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_USERAGENT      => 'NovelVerse/1.0',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($res === false || $code >= 400) return [];
    return json_decode($res, true) ?: [];
}

switch ($action) {

    case 'search':
        $q = trim($_GET['q'] ?? '');
        if ($q === '') json_out(['error' => 'Missing ?q='], 400);
        $data = mh_get('/search', ['q' => $q]);
        json_out(['data' => $data['data'] ?? []]);

    case 'detail':
        $id = trim($_GET['id'] ?? '');
        if ($id === '') json_out(['error' => 'Missing ?id='], 400);
        // reuse search-style data if needed; for now return basic
        json_out(['data' => ['id' => $id]]);

    case 'chapters':
        $id = trim($_GET['id'] ?? '');
        if ($id === '') json_out(['error' => 'Missing ?id='], 400);
        $data = mh_get('/chapters/' . rawurlencode($id));
        json_out(['data' => $data['data'] ?? []]);

    case 'pages':
        $chapterId = trim($_GET['chapterId'] ?? '');
        if ($chapterId === '') json_out(['error' => 'Missing ?chapterId='], 400);
        $data = mh_get('/pages/' . rawurlencode($chapterId));
        json_out(['data' => [
            'pages' => $data['data']['pages'] ?? []
        ]]);

    default:
        json_out(['error' => 'Unknown action. Use: search | chapters | pages'], 400);
}
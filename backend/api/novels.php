<?php
/**
 * Novels API
 * - Gutendex (Project Gutenberg) = public domain full text
 * - Later: AniList metadata, manual uploads
 */
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';

function nv_get(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'NovelVerse/1.0 (school project)',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($res === false || $code >= 400) return [];
    return json_decode($res, true) ?: [];
}

function gutendex_plain_text_url(array $formats): ?string {
    // Prefer plain text, then HTML
    foreach ($formats as $mime => $url) {
        if (str_contains($mime, 'text/plain') && !str_contains($mime, 'zip')) {
            return $url;
        }
    }
    foreach ($formats as $mime => $url) {
        if (str_contains($mime, 'text/html') && !str_contains($mime, 'zip')) {
            return $url;
        }
    }
    return null;
}

switch ($action) {

    // Search Gutendex: ?action=search&q=pride
    case 'search':
        $q = trim($_GET['q'] ?? '');
        if ($q === '') json_out(['error' => 'Missing ?q='], 400);

        $data = nv_get('https://gutendex.com/books?search=' . rawurlencode($q));
        $out  = [];

        foreach ($data['results'] ?? [] as $b) {
            $formats = $b['formats'] ?? [];
            $cover = $formats['image/jpeg'] ?? null;
            $author = $b['authors'][0]['name'] ?? '';
            $desc = $b['summaries'][0] ?? '';

            $out[] = [
                'id'     => (string)($b['id'] ?? ''),
                'title'  => $b['title'] ?? 'Untitled',
                'cover'  => $cover,
                'desc'   => $desc,
                'status' => 'completed',
                'type'   => 'novel',
                'author' => $author,
                'tags'   => array_slice($b['subjects'] ?? [], 0, 8),
                '_source'=> 'gutendex',
            ];
        }
        json_out(['data' => $out]);

    // Detail: ?action=detail&id=1342
    case 'detail':
        $id = trim($_GET['id'] ?? '');
        if ($id === '') json_out(['error' => 'Missing ?id='], 400);

        $b = nv_get('https://gutendex.com/books/' . rawurlencode($id));
        if (empty($b['id'])) json_out(['error' => 'Not found'], 404);

        $formats = $b['formats'] ?? [];
        json_out(['data' => [
            'id'     => (string)$b['id'],
            'title'  => $b['title'] ?? 'Untitled',
            'cover'  => $formats['image/jpeg'] ?? null,
            'desc'   => $b['summaries'][0] ?? '',
            'status' => 'completed',
            'type'   => 'novel',
            'author' => $b['authors'][0]['name'] ?? '',
            'tags'   => array_slice($b['subjects'] ?? [], 0, 8),
            'text_url' => gutendex_plain_text_url($formats),
        ]]);

    // Chapters: Gutendex books are single-volume → 1 "chapter"
    case 'chapters':
        $id = trim($_GET['id'] ?? '');
        if ($id === '') json_out(['error' => 'Missing ?id='], 400);

        json_out(['data' => [[
            'id'      => $id,          // same as book id
            'chapter' => '1',
            'title'   => 'Full text',
        ]]]);

    // Content: ?action=content&chapterId=1342  (book id)
    case 'content':
        $id = trim($_GET['chapterId'] ?? $_GET['id'] ?? '');
        if ($id === '') json_out(['error' => 'Missing ?chapterId='], 400);

        $b = nv_get('https://gutendex.com/books/' . rawurlencode($id));
        $formats = $b['formats'] ?? [];
        $textUrl = gutendex_plain_text_url($formats);

        if (!$textUrl) {
            json_out(['data' => ['content' => '', 'title' => $b['title'] ?? '']]);
        }

        // Fetch plain text
        $ch = curl_init($textUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 40,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'NovelVerse/1.0',
        ]);
        $text = curl_exec($ch);
        curl_close($ch);

        // Limit size for browser (first ~200KB)
        if (is_string($text) && strlen($text) > 200000) {
            $text = substr($text, 0, 200000) . "\n\n[... truncated for demo ...]";
        }

        // Strip some Gutenberg header noise lightly
        if (is_string($text)) {
            $text = preg_replace('/\*\*\* START OF.*?\*\*\*/s', '', $text);
            $text = trim($text);
        }

        json_out(['data' => [
            'content' => $text ?: '',
            'title'   => $b['title'] ?? 'Full text',
        ]]);

    default:
        json_out(['error' => 'Unknown action. Use: search | detail | chapters | content'], 400);
}
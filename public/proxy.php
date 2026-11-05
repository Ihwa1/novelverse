<?php
// public/proxy.php - Bypasses hotlinking and referrer blocks
$url = $_GET['url'] ?? '';

if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit('Invalid URL');
}

$host = parse_url($url, PHP_URL_HOST) ?? '';
$allowedDomains = ['mangadex.org', 'comick.pictures', 'comick.dev', 'anilist.co'];

$isAllowed = false;
foreach ($allowedDomains as $domain) {
    if ($host === $domain || (strlen($host) > strlen($domain) && substr($host, -strlen('.' . $domain)) === '.' . $domain)) {
        $isAllowed = true;
        break;
    }
}

if (!$isAllowed) {
    http_response_code(403);
    exit('Domain not allowed');
}

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    CURLOPT_HTTPHEADER => ['Referer: https://comickz.co.uk/'],
]);

$imageData = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $imageData) {
    header("Content-Type: " . ($contentType ?: "image/jpeg"));
    header("Cache-Control: public, max-age=86400");
    echo $imageData;
} else {
    http_response_code(404);
    echo "Failed to load image";
}
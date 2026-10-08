<?php
// 🧪 test_md.php — delete this file after testing!

$chapterId = '8d15e8cc-d91d-490e-9c09-dc381cf2bff8';

echo '<h2>MangaDex API Diagnostic</h2>';

// Test 1: cURL
echo '<h3>Test 1: cURL</h3>';
$ch = curl_init('https://api.mangadex.org/chapter/' . $chapterId);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_USERAGENT      => 'NovelVerse/1.0 (localhost dev)',
]);
$r = curl_exec($ch);
echo 'HTTP code: <b>' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . '</b><br>';
echo 'cURL error: <b>' . (curl_error($ch) ?: 'none ✅') . '</b><br>';
echo 'Response preview:<br><pre style="background:#111;color:#0f0;padding:10px;white-space:pre-wrap;">';
echo htmlspecialchars(substr($r ?: '(no body)', 0, 500));
echo '</pre>';
curl_close($ch);

// Test 2: file_get_contents (what upload.php was using)
echo '<h3>Test 2: file_get_contents</h3>';
$r2 = @file_get_contents('https://api.mangadex.org/chapter/' . $chapterId);
echo $r2 === false
    ? '❌ FAILED — this is why upload.php said "chapter not found". allow_url_fopen or SSL is blocking it.'
    : '✅ Works! Response preview: ' . htmlspecialchars(substr($r2, 0, 300));

// Test 3: allow_url_fopen setting
echo '<h3>Test 3: PHP settings</h3>';
echo 'allow_url_fopen: <b>' . ini_get('allow_url_fopen') . '</b><br>';
echo 'openssl cafile: <b>' . (ini_get('openssl.cafile') ?: '(not set)') . '</b><br>';
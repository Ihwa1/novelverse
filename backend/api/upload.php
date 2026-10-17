
    <?php
    require_once __DIR__ . '/../db.php';

    $pdo = db();
    $action = $_GET['action'] ?? '';

    // Authors and admins only.
    $u = current_user();

    if (
        !$u ||
        (($u['role'] ?? '') !== 'author' &&
        (int)($u['is_admin'] ?? 0) !== 1)
    ) {
        json_out(['error' => 'Author account required'], 403);
    }

    // Upload directories.
    $coverDir = __DIR__ . '/../../public/uploads/covers/';
    $chapDir  = __DIR__ . '/../../public/uploads/chapters/';

    foreach ([$coverDir, $chapDir] as $d) {
        if (
            !is_dir($d) &&
            !mkdir($d, 0755, true) &&
            !is_dir($d)
        ) {
            json_out(['error' => 'Could not create upload directory'], 500);
        }
    }

    /**
     * Validate and save an uploaded image.
     *
     * Allowed types: JPG, PNG, WEBP, GIF.
     * Maximum size: 10 MB per image.
     */
    function save_image(array $file, string $dir): string
    {
        if (
            !isset($file['error'], $file['tmp_name'], $file['size']) ||
            $file['error'] !== UPLOAD_ERR_OK ||
            !is_uploaded_file($file['tmp_name'])
        ) {
            json_out(['error' => 'Invalid image upload'], 422);
        }

        // Limit each image to 10 MB.
        if (
            !is_numeric($file['size']) ||
            (int)$file['size'] <= 0 ||
            (int)$file['size'] > 10 * 1024 * 1024
        ) {
            json_out(
                ['error' => 'Each image must be larger than 0 bytes and no larger than 10 MB'],
                422
            );
        }

        // Validate the actual MIME type instead of trusting the filename.
        if (!class_exists('finfo')) {
            error_log('PHP Fileinfo extension is not enabled.');
            json_out(['error' => 'Image validation is unavailable on the server'], 500);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];

        if (!isset($allowedTypes[$mime])) {
            json_out(
                ['error' => 'Only JPG, PNG, WEBP, and GIF images are allowed'],
                422
            );
        }

        // Use a random filename to avoid filename collisions.
        $name = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];

        $destination = rtrim($dir, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            json_out(['error' => 'Failed to save uploaded image'], 500);
        }

        return $name;
    }


    // ============================================================
    // CREATE A SERIES
    // POST upload.php?action=series
    // Requires multipart/form-data.
    // ============================================================
    if (
        $action === 'series' &&
        $_SERVER['REQUEST_METHOD'] === 'POST'
    ) {
        $title = trim($_POST['title'] ?? '');
        $type  = $_POST['type'] ?? '';

        if (
            $title === '' ||
            !in_array($type, ['manga', 'manhwa', 'manhua', 'novel'], true)
        ) {
            json_out(['error' => 'Title and a valid type are required'], 422);
        }

        $isNovel = ($type === 'novel') ? 1 : 0;

        // Optional cover image.
        $cover = null;

        if (isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
            $cover = 'uploads/covers/' . save_image(
                $_FILES['cover'],
                $coverDir
            );
        }

        // Validate series status.
        $status = $_POST['status'] ?? 'ongoing';

        if (!in_array($status, ['ongoing', 'completed', 'hiatus'], true)) {
            $status = 'ongoing';
        }

        // Use the logged-in account's username as the author display name.
        // The created_by field below is the authoritative ownership identifier.
        $authorName = $u['username'];

        $novelPrice = $isNovel
            ? max(0, (float)($_POST['novel_price'] ?? 0))
            : 0;

        $freeChapters = max(0, (int)($_POST['free_chapters'] ?? 3));

        try {
            $slug = slugify($title);

            $st = $pdo->prepare(
                'INSERT INTO series
                    (title, slug, type, description, cover_url, author,
                    status, is_novel, novel_price, free_chapters, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $st->execute([
                $title,
                $slug,
                $type,
                trim($_POST['description'] ?? ''),
                $cover,
                $authorName,
                $status,
                $isNovel,
                $novelPrice,
                $freeChapters,
                $u['id']
            ]);

            json_out([
                'data' => [
                    'id'   => $pdo->lastInsertId(),
                    'slug' => $slug
                ]
            ], 201);

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                json_out(
                    ['error' => 'A series with that title or slug already exists'],
                    409
                );
            }

            error_log($e->getMessage());
            json_out(['error' => 'A server error occurred while creating the series'], 500);
        }
    }


    // ============================================================
    // ADD A CHAPTER
    // POST upload.php?action=chapter
    // Requires multipart/form-data.
    // ============================================================
    if (
        $action === 'chapter' &&
        $_SERVER['REQUEST_METHOD'] === 'POST'
    ) {
        $seriesId = (int)($_POST['series_id'] ?? 0);

        $st = $pdo->prepare('SELECT * FROM series WHERE id = ?');
        $st->execute([$seriesId]);
        $series = $st->fetch(PDO::FETCH_ASSOC);

        if (!$series) {
            json_out(['error' => 'Series not found'], 404);
        }

        // Authors can only add chapters to their own series.
        // Admins retain the existing administrative override.
        if (
            (int)$series['created_by'] !== (int)$u['id'] &&
            (int)($u['is_admin'] ?? 0) !== 1
        ) {
            json_out(['error' => 'This is not your series'], 403);
        }

        $num   = trim($_POST['chapter_number'] ?? '');
        $title = trim($_POST['title'] ?? '');

        if ($num === '') {
            json_out(['error' => 'Chapter number is required'], 422);
        }

        if (!is_numeric($num) || (float)$num < 0) {
            json_out(['error' => 'Chapter number must be a valid non-negative number'], 422);
        }

        // Chapter content fields.
        $mdChapterId = trim($_POST['md_chapter_id'] ?? '');
        $externalId  = null;
        $pagesJson   = null;
        $content     = null;

        // --------------------------------------------------------
        // NOVEL: Store text content.
        // --------------------------------------------------------
        if ((int)$series['is_novel'] === 1) {
            $content = trim($_POST['content'] ?? '');

            if ($content === '') {
                json_out(['error' => 'Novel chapters need text content'], 422);
            }
        }

        // --------------------------------------------------------
        // MANGADEX: Import an existing chapter.
        // --------------------------------------------------------
        elseif ($mdChapterId !== '') {
            // Validate the expected MangaDex chapter ID format.
            if (!preg_match(
                '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
                $mdChapterId
            )) {
                json_out(['error' => 'Invalid MangaDex chapter ID'], 422);
            }

            $ch = curl_init(
                'https://api.mangadex.org/chapter/' . rawurlencode($mdChapterId)
            );

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT       => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_USERAGENT     => 'NovelVerse/1.0 (localhost dev)',
            ]);

            $check = curl_exec($ch);
            $http  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $cerr  = curl_error($ch);

            curl_close($ch);

            if ($check === false) {
                error_log('MangaDex request failed: ' . $cerr);
                json_out(['error' => 'Could not reach the MangaDex API'], 502);
            }

            if ($http === 404) {
                json_out(['error' => 'MangaDex chapter not found. Check the ID'], 422);
            }

            if ($http !== 200) {
                json_out(['error' => "MangaDex API returned HTTP $http"], 502);
            }

            $response = json_decode($check, true);

            if (
                !is_array($response) ||
                !isset($response['result']) ||
                $response['result'] !== 'ok'
            ) {
                json_out(['error' => 'MangaDex returned an invalid chapter response'], 502);
            }

            $externalId = $mdChapterId;
        }

        // --------------------------------------------------------
        // COMIC: Upload multiple page images.
        // --------------------------------------------------------
        else {
            if (
                !isset($_FILES['pages']['name']) ||
                !is_array($_FILES['pages']['name']) ||
                empty($_FILES['pages']['name'])
            ) {
                json_out(
                    ['error' => 'Upload at least one page image or provide a MangaDex chapter ID'],
                    422
                );
            }

            $pages = [];

            foreach ($_FILES['pages']['name'] as $i => $name) {
                // Skip empty file inputs.
                if ($name === '') {
                    continue;
                }

                $file = [
                    'name'     => $name,
                    'tmp_name' => $_FILES['pages']['tmp_name'][$i] ?? '',
                    'error'    => $_FILES['pages']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                    'size'     => $_FILES['pages']['size'][$i] ?? 0
                ];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    json_out(
                        ['error' => 'One or more comic page images failed to upload'],
                        422
                    );
                }

                $pages[] = 'uploads/chapters/' . save_image($file, $chapDir);
            }

            if (!$pages) {
                json_out(['error' => 'No valid page images uploaded'], 422);
            }

            // Preserve the upload order.
            $pagesJson = json_encode($pages);

            if ($pagesJson === false) {
                json_out(['error' => 'Could not process comic page images'], 500);
            }
        }

        // The first free_chapters chapters are free.
        $isFree = ((float)$num <= (int)$series['free_chapters']) ? 1 : 0;

        try {
            $st = $pdo->prepare(
                'INSERT INTO chapters
                    (series_id, chapter_number, title, is_free,
                    pages_json, content, external_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)'
            );

            $st->execute([
                $series['id'],
                $num,
                $title,
                $isFree,
                $pagesJson,
                $content,
                $externalId
            ]);

            json_out([
                'data' => [
                    'id'      => $pdo->lastInsertId(),
                    'is_free' => (bool)$isFree
                ]
            ], 201);

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                json_out(
                    ['error' => 'That chapter number already exists for this series'],
                    409
                );
            }

            error_log($e->getMessage());
            json_out(['error' => 'A server error occurred while adding the chapter'], 500);
        }
    }


    // ============================================================
    // UNKNOWN ACTION
    // ============================================================
    json_out(['error' => 'Unknown action'], 400);


<?php
require_once __DIR__ . '/../includes/init.php';

// Allow Authors and Admins only.
if (
    !$nv_user ||
    (
        ($nv_user['role'] ?? '') !== 'author' &&
        (int)($nv_user['is_admin'] ?? 0) !== 1
    )
) {
    header('Location: /novelverse/public/index.php');
    exit;
}

$pdo = pub_db();
$isAdmin = (int)($nv_user['is_admin'] ?? 0) === 1;

$seriesId = filter_input(INPUT_GET, 'series_id', FILTER_VALIDATE_INT);

if (!$seriesId || $seriesId < 1) {
    header('Location: my-series.php');
    exit;
}

// Load the selected series.
$stmt = $pdo->prepare('SELECT * FROM series WHERE id = ? LIMIT 1');
$stmt->execute([$seriesId]);
$series = $stmt->fetch();

if (!$series) {
    http_response_code(404);
    exit('Series not found.');
}

// Authors can manage only their own series.
// Admins can manage any series.
if (
    (int)($series['created_by'] ?? 0) !== (int)$nv_user['id'] &&
    !$isAdmin
) {
    header('Location: my-series.php');
    exit;
}

// Load existing chapters.
$stmt = $pdo->prepare(
    'SELECT id, chapter_number, title, is_free
     FROM chapters
     WHERE series_id = ?
     ORDER BY chapter_number ASC, id ASC'
);
$stmt->execute([$seriesId]);
$chapters = $stmt->fetchAll();

$isNovel = (int)$series['is_novel'] === 1;

$pageTitle = 'Manage Chapters';
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container py-5">

    <!-- Breadcrumb -->
    <div class="mb-4">
        <a href="my-series.php" class="text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>
            Back to My Series
        </a>
    </div>

    <!-- Series Information -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-3">

                <div>
                    <span class="badge bg-secondary mb-2">
                        <?= htmlspecialchars(ucfirst($series['type'])) ?>
                    </span>

                    <h1 class="fw-bold mb-2">
                        <?= htmlspecialchars($series['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <p class="text-muted mb-0">
                        <?= count($chapters) ?> chapter(s) uploaded
                    </p>
                </div>

                <div>
                    <span class="badge bg-info text-dark">
                        <?= $isNovel ? 'Text Novel' : 'Comic Series' ?>
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- Upload Chapter Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">

            <h4 class="fw-bold mb-3">
                <i class="bi bi-cloud-upload me-2"></i>
                Add a Chapter
            </h4>

            <div id="chapterMessage"
                 class="alert d-none"
                 role="alert"></div>

            <form id="chapterForm" enctype="multipart/form-data">

                <input type="hidden"
                       name="series_id"
                       value="<?= (int)$series['id'] ?>">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="chapter_number"
                               class="form-label fw-semibold">
                            Chapter Number *
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="chapter_number"
                            name="chapter_number"
                            min="0"
                            max="999999"
                            step="0.01"
                            placeholder="e.g. 1"
                            required
                        >
                    </div>

                    <div class="col-md-8">
                        <label for="chapter_title"
                               class="form-label fw-semibold">
                            Chapter Title
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="chapter_title"
                            name="title"
                            maxlength="255"
                            placeholder="e.g. The Beginning"
                        >
                    </div>

                </div>

                <?php if ($isNovel): ?>

                    <!-- Novel text -->
                    <div class="mt-3">
                        <label for="content"
                               class="form-label fw-semibold">
                            Chapter Content *
                        </label>

                        <textarea
                            class="form-control"
                            id="content"
                            name="content"
                            rows="12"
                            placeholder="Write your novel chapter here..."
                            required
                        ></textarea>

                        <div class="form-text">
                            Enter the chapter text. You can format it
                            with paragraphs and line breaks.
                        </div>
                    </div>

                <?php else: ?>

                    <!-- Comic chapter source -->
                    <div class="mt-4">
                        <label class="form-label fw-semibold">
                            Chapter Source *
                        </label>

                        <select id="chapterSource"
                                class="form-select">
                            <option value="images">
                                Upload Page Images
                            </option>
                            <option value="mangadex">
                                Import from MangaDex
                            </option>
                        </select>
                    </div>

                    <!-- Page images -->
                    <div id="imageUploadSection" class="mt-3">
                        <label for="pages"
                               class="form-label fw-semibold">
                            Page Images *
                        </label>

                        <input
                            type="file"
                            class="form-control"
                            id="pages"
                            name="pages[]"
                            accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"
                            multiple
                        >

                        <div class="form-text">
                            Select multiple images. Their selection order
                            will be used as the page order.
                            Each image must be no larger than 10 MB.
                        </div>
                    </div>

                    <!-- MangaDex chapter ID -->
                    <div id="mangadexSection" class="mt-3 d-none">
                        <label for="md_chapter_id"
                               class="form-label fw-semibold">
                            MangaDex Chapter ID *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="md_chapter_id"
                            name="md_chapter_id"
                            placeholder="Paste the MangaDex chapter UUID"
                        >

                        <div class="form-text">
                            Enter the chapter UUID from MangaDex.
                            The API validates that the chapter exists.
                        </div>
                    </div>

                <?php endif; ?>

                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button type="submit"
                            id="uploadButton"
                            class="btn btn-nv">
                        <i class="bi bi-cloud-upload me-1"></i>
                        Upload Chapter
                    </button>

                    <a href="my-series.php"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

    <!-- Existing Chapters -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <h4 class="fw-bold mb-3">Existing Chapters</h4>

            <?php if (empty($chapters)): ?>

                <div class="text-center py-4">
                    <i class="bi bi-journal-text fs-1 text-muted"></i>
                    <h5 class="mt-3">No chapters yet</h5>
                    <p class="text-muted mb-0">
                        Upload your first chapter using the form above.
                    </p>
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Chapter</th>
                                <th>Title</th>
                                <th>Access</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($chapters as $chapter): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars(
                                        (string)$chapter['chapter_number'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $chapter['title'] ?: 'Untitled Chapter',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?php if ((int)$chapter['is_free'] === 1): ?>
                                        <span class="badge bg-success">
                                            Free
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            Paid
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('chapterForm');
    const message = document.getElementById('chapterMessage');
    const button = document.getElementById('uploadButton');

    const seriesId = <?= (int)$series['id'] ?>;
    const isNovel = <?= $isNovel ? 'true' : 'false' ?>;

    const chapterSource = document.getElementById('chapterSource');
    const imageSection = document.getElementById('imageUploadSection');
    const mangadexSection = document.getElementById('mangadexSection');
    const pagesInput = document.getElementById('pages');
    const mdInput = document.getElementById('md_chapter_id');

    function showMessage(text, type) {
        message.textContent = text;
        message.className = 'alert alert-' + type;
        message.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    if (!isNovel) {
        function updateSource() {
            const useMangaDex = chapterSource.value === 'mangadex';

            imageSection.classList.toggle('d-none', useMangaDex);
            mangadexSection.classList.toggle('d-none', !useMangaDex);

            pagesInput.disabled = useMangaDex;
            pagesInput.required = !useMangaDex;

            mdInput.disabled = !useMangaDex;
            mdInput.required = useMangaDex;
        }

        chapterSource.addEventListener('change', updateSource);
        updateSource();
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        message.className = 'alert d-none';

        const formData = new FormData(form);

        button.disabled = true;
        button.textContent = 'Uploading...';

        try {
            const response = await fetch(
                '../../backend/api/upload.php?action=chapter',
                {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error(
                    result.error || 'Unable to upload the chapter.'
                );
            }

            showMessage(
                'Chapter uploaded successfully! Refreshing the chapter list...',
                'success'
            );

            window.setTimeout(function () {
                window.location.href =
                    'manage-chapters.php?series_id=' +
                    encodeURIComponent(seriesId);
            }, 1000);

        } catch (error) {
            showMessage(
                error.message ||
                'A network error occurred. Please try again.',
                'danger'
            );
        } finally {
            button.disabled = false;
            button.innerHTML =
                '<i class="bi bi-cloud-upload me-1"></i> Upload Chapter';
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

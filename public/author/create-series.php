
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

$pageTitle = 'Create Series';
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container py-5">

    <!-- Page Heading -->
    <div class="mb-4">
        <a href="dashboard.php"
           class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Dashboard
        </a>

        <h1 class="fw-bold mt-3">Create a New Series</h1>
        <p class="text-muted">
            Publish your manga, manhwa, manhua, or novel on NovelVerse.
        </p>
    </div>

    <!-- Response Message -->
    <div id="formMessage"
         class="alert d-none"
         role="alert"></div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form id="createSeriesForm"
                  enctype="multipart/form-data">

                <!-- Series Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">
                        Series Title <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="title"
                        name="title"
                        maxlength="255"
                        placeholder="Enter your series title"
                        required
                    >
                </div>

                <!-- Series Type -->
                <div class="mb-3">
                    <label for="type" class="form-label fw-semibold">
                        Series Type <span class="text-danger">*</span>
                    </label>

                    <select
                        class="form-select"
                        id="type"
                        name="type"
                        required
                    >
                        <option value="">Select a type</option>
                        <option value="manga">Manga</option>
                        <option value="manhwa">Manhwa</option>
                        <option value="manhua">Manhua</option>
                        <option value="novel">Novel</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description"
                           class="form-label fw-semibold">
                        Description
                    </label>

                    <textarea
                        class="form-control"
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Write a description of your series..."
                    ></textarea>
                </div>

                <!-- Cover Image -->
                <div class="mb-3">
                    <label for="cover" class="form-label fw-semibold">
                        Cover Image
                    </label>

                    <input
                        type="file"
                        class="form-control"
                        id="cover"
                        name="cover"
                        accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"
                    >

                    <div class="form-text">
                        JPG, PNG, WEBP, or GIF. Maximum size: 10 MB.
                    </div>

                    <!-- Cover Preview -->
                    <div id="coverPreviewContainer"
                         class="mt-3 d-none">
                        <img
                            id="coverPreview"
                            src=""
                            alt="Cover preview"
                            class="rounded border"
                            style="width:150px;height:210px;object-fit:cover;"
                        >
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">
                        Publication Status
                    </label>

                    <select
                        class="form-select"
                        id="status"
                        name="status"
                    >
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="hiatus">Hiatus</option>
                    </select>
                </div>

                <!-- Novel Settings -->
                <div id="novelSettings"
                     class="border rounded p-3 mb-4 d-none">

                    <h5 class="fw-bold mb-3">Novel Settings</h5>

                    <div class="mb-3">
                        <label for="novel_price"
                               class="form-label fw-semibold">
                            Chapter Price (₱)
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="novel_price"
                            name="novel_price"
                            min="0"
                            max="99999999"
                            step="0.01"
                            value="0.00"
                        >

                        <div class="form-text">
                            Set the price used by your novel's chapter
                            purchasing feature.
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="free_chapters"
                               class="form-label fw-semibold">
                            Number of Free Chapters
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="free_chapters"
                            name="free_chapters"
                            min="0"
                            max="100000"
                            value="3"
                        >

                        <div class="form-text">
                            For example, entering 3 makes chapters 1–3 free.
                        </div>
                    </div>

                </div>

                <!-- Comic Free Chapters -->
                <div id="comicFreeSettings" class="mb-4">
                    <label for="comic_free_chapters"
                           class="form-label fw-semibold">
                        Number of Free Chapters
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        id="comic_free_chapters"
                        min="0"
                        max="100000"
                        value="3"
                    >

                    <div class="form-text">
                        The first specified number of chapters will be free.
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-flex flex-wrap gap-2">
                    <button
                        type="submit"
                        class="btn btn-nv"
                        id="submitButton"
                    >
                        <i class="bi bi-plus-circle me-1"></i>
                        Create Series
                    </button>

                    <a href="dashboard.php"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createSeriesForm');
    const typeInput = document.getElementById('type');
    const novelSettings = document.getElementById('novelSettings');
    const comicFreeSettings = document.getElementById('comicFreeSettings');
    const freeChapters = document.getElementById('free_chapters');
    const comicFreeChapters = document.getElementById('comic_free_chapters');
    const coverInput = document.getElementById('cover');
    const previewContainer = document.getElementById('coverPreviewContainer');
    const preview = document.getElementById('coverPreview');
    const message = document.getElementById('formMessage');
    const submitButton = document.getElementById('submitButton');

    function updateTypeSettings() {
        const isNovel = typeInput.value === 'novel';

        novelSettings.classList.toggle('d-none', !isNovel);
        comicFreeSettings.classList.toggle('d-none', isNovel);

        freeChapters.disabled = !isNovel;
        comicFreeChapters.disabled = isNovel;
    }

    function showMessage(text, type) {
        message.textContent = text;
        message.className = 'alert alert-' + type;
        message.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    typeInput.addEventListener('change', updateTypeSettings);
    updateTypeSettings();

    // Preview the selected cover image.
    coverInput.addEventListener('change', function () {
        const file = coverInput.files[0];

        if (!file) {
            previewContainer.classList.add('d-none');
            preview.removeAttribute('src');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            coverInput.value = '';
            previewContainer.classList.add('d-none');
            showMessage('The cover image must not exceed 10 MB.', 'warning');
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif'
        ];

        if (!allowedTypes.includes(file.type)) {
            coverInput.value = '';
            previewContainer.classList.add('d-none');
            showMessage(
                'Please select a JPG, PNG, WEBP, or GIF image.',
                'warning'
            );
            return;
        }

        preview.src = URL.createObjectURL(file);
        previewContainer.classList.remove('d-none');
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        message.classList.add('d-none');

        const formData = new FormData(form);

        // Send the correct free-chapter setting for the selected type.
        formData.set(
            'free_chapters',
            typeInput.value === 'novel'
                ? freeChapters.value
                : comicFreeChapters.value
        );

        // Avoid duplicate submissions.
        submitButton.disabled = true;
        submitButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>' +
            'Creating...';

        try {
            const response = await fetch(
                '../../backend/api/upload.php?action=series',
                {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error(
                    result.error || 'Unable to create the series.'
                );
            }

            showMessage(
                'Series created successfully! Opening My Series...',
                'success'
            );

            form.reset();
            previewContainer.classList.add('d-none');
            updateTypeSettings();

            // Navigate after a successful response.
            window.setTimeout(function () {
                window.location.href = 'my-series.php';
            }, 1200);

        } catch (error) {
            showMessage(
                error.message || 'A network error occurred. Please try again.',
                'danger'
            );
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML =
                '<i class="bi bi-plus-circle me-1"></i> Create Series';
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

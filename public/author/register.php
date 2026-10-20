
<?php
require_once __DIR__ . '/../includes/init.php';

$pageTitle = 'Become an Author';
$isLoggedIn = !empty($_SESSION['user_id']);

require_once __DIR__ . '/../includes/header.php';
?>

<style>
/* NovelVerse Author Application — scoped styles */
.author-page {
    --author-bg: #11121a;
    --author-card: #1c1d29;
    --author-input: #141520;
    --author-border: #39394c;
    --author-text: #f5f3ff;
    --author-muted: #c1bfd1;
    --author-accent: #8b5cf6;
    --author-accent-hover: #7c3aed;

    color: var(--author-text);
    min-height: 75vh;
    padding: 48px 16px 72px;
    background:
        radial-gradient(ellipse at 50% 0%, rgba(139, 92, 246, .15), transparent 48%),
        var(--author-bg);
}

.author-page * {
    box-sizing: border-box;
}

.author-page .author-container {
    width: 100%;
    max-width: 820px;
    margin: 0 auto;
}

.author-page .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--author-muted);
    text-decoration: none;
    font-size: .92rem;
    margin-bottom: 24px;
    transition: color .2s ease;
}

.author-page .back-link:hover {
    color: #c4b5fd;
}

.author-page .application-card {
    overflow: hidden;
    border: 1px solid var(--author-border);
    border-radius: 24px;
    background: var(--author-card);
    box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
}

.author-page .hero {
    position: relative;
    padding: 38px 36px 30px;
    border-bottom: 1px solid var(--author-border);
    background: linear-gradient(135deg, rgba(139, 92, 246, .16), transparent 72%);
}

.author-page .hero-icon {
    display: grid;
    place-items: center;
    width: 64px;
    height: 64px;
    margin-bottom: 22px;
    border: 1px solid rgba(196, 181, 253, .3);
    border-radius: 20px;
    background: rgba(139, 92, 246, .16);
    color: #c4b5fd;
    font-size: 1.8rem;
}

.author-page .eyebrow {
    margin-bottom: 10px;
    color: #c4b5fd;
    font-size: .76rem;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.author-page .hero h1 {
    margin: 0 0 12px;
    color: var(--author-text);
    font-size: clamp(1.8rem, 4vw, 2.45rem);
    font-weight: 800;
    letter-spacing: -.04em;
}

.author-page .hero p {
    max-width: 580px;
    margin: 0;
    color: var(--author-muted);
    line-height: 1.7;
}

.author-page .form-content {
    padding: 32px 36px 36px;
}

.author-page .section-heading {
    margin-bottom: 24px;
}

.author-page .section-heading h2 {
    margin: 0 0 7px;
    color: var(--author-text);
    font-size: 1.15rem;
    font-weight: 750;
}

.author-page .section-heading p {
    margin: 0;
    color: var(--author-muted);
    font-size: .9rem;
}

.author-page .form-label {
    display: block;
    margin-bottom: 9px;
    color: var(--author-text);
    font-size: .92rem;
    font-weight: 650;
}

.author-page .required-mark {
    color: #c4b5fd;
}

.author-page .form-control {
    width: 100%;
    min-height: 48px;
    padding: 12px 14px;
    border: 1px solid var(--author-border);
    border-radius: 12px;
    outline: none;
    background: var(--author-input);
    color: var(--author-text);
    font: inherit;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.author-page textarea.form-control {
    min-height: 145px;
    resize: vertical;
}

.author-page .form-control::placeholder {
    color: #89879d;
    opacity: 1;
}

.author-page .form-control:focus {
    border-color: var(--author-accent);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, .18);
    background: var(--author-input);
    color: var(--author-text);
}

.author-page .field-help {
    margin-top: 8px;
    color: var(--author-muted);
    font-size: .82rem;
    line-height: 1.5;
}

.author-page .form-group {
    margin-bottom: 23px;
}

.author-page .notice {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 26px 0;
    padding: 16px;
    border: 1px solid rgba(139, 92, 246, .28);
    border-radius: 14px;
    background: rgba(139, 92, 246, .08);
    color: var(--author-muted);
    font-size: .88rem;
    line-height: 1.65;
}

.author-page .notice i {
    margin-top: 2px;
    color: #c4b5fd;
    font-size: 1.1rem;
}

.author-page .submit-button {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    width: 100%;
    min-height: 52px;
    border: 0;
    border-radius: 13px;
    background: var(--author-accent);
    color: #fff;
    font-size: .98rem;
    font-weight: 750;
    transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
}

.author-page .submit-button:hover:not(:disabled) {
    transform: translateY(-1px);
    background: var(--author-accent-hover);
    box-shadow: 0 9px 24px rgba(139, 92, 246, .24);
}

.author-page .submit-button:disabled {
    cursor: wait;
    opacity: .7;
}

.author-page .login-panel {
    padding: 28px;
    border: 1px solid var(--author-border);
    border-radius: 16px;
    background: var(--author-input);
}

.author-page .login-panel p {
    color: var(--author-muted);
    line-height: 1.7;
}

.author-page .login-button {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    min-height: 46px;
    padding: 10px 18px;
    border-radius: 12px;
    background: var(--author-accent);
    color: white;
    font-weight: 700;
    text-decoration: none;
}

.author-page .login-button:hover {
    background: var(--author-accent-hover);
    color: white;
}

.author-page .status-panel {
    margin-bottom: 26px;
    padding: 17px 18px;
    border: 1px solid var(--author-border);
    border-radius: 14px;
    background: var(--author-input);
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.author-page .status-panel h3 {
    margin: 0 0 5px;
    color: var(--author-text);
    font-size: 1rem;
    font-weight: 750;
}

.author-page .status-panel p {
    margin: 0;
    color: var(--author-muted);
    white-space: pre-wrap;
}

.author-page .status-panel.pending {
    border-color: rgba(251, 191, 36, .4);
}

.author-page .status-panel.approved {
    border-color: rgba(52, 211, 153, .4);
}

.author-page .status-panel.rejected {
    border-color: rgba(248, 113, 113, .4);
}

.author-page .alert {
    border-radius: 12px;
    overflow-wrap: anywhere;
}

.author-page .d-none {
    display: none !important;
}

@media (max-width: 600px) {
    .author-page {
        padding: 28px 12px 48px;
    }

    .author-page .hero {
        padding: 28px 22px 24px;
    }

    .author-page .form-content {
        padding: 25px 22px 28px;
    }

    .author-page .application-card {
        border-radius: 18px;
    }

    .author-page .hero-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        font-size: 1.55rem;
    }
}
</style>

<main class="author-page">
    <div class="author-container">

        <a href="../index.php" class="back-link">
    <i class="bi bi-arrow-left"></i>
    Back to NovelVerse
</a>

        <section class="application-card">

            <header class="hero">
                <div class="hero-icon">
                    <i class="bi bi-feather"></i>
                </div>

                <div class="eyebrow">Creator Community</div>

                <h1>Become a NovelVerse Author</h1>

                <p>
                    Have a story worth sharing? Introduce yourself, tell us
                    about your writing, and apply to publish with the
                    NovelVerse community.
                </p>
            </header>

            <div class="form-content">

                <?php if (!$isLoggedIn): ?>

                    <div class="login-panel">
                        <h2 class="h5 fw-bold mb-3">Log in to get started</h2>
                        <p>
                            You need a NovelVerse account to submit an author
                            application. After logging in, return here to
                            complete your application.
                        </p>
                        <a href="/novelverse/public/index.php"
                           class="login-button">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Go to Login
                        </a>
                    </div>

                <?php else: ?>

                    <div id="applicationAlert"
                         class="alert d-none"
                         role="alert"
                         aria-live="polite"></div>

                    <div id="applicationStatus"
                         class="status-panel d-none"
                         role="status"
                         aria-live="polite">
                        <h3 id="statusTitle"></h3>
                        <p id="statusMessage"></p>
                    </div>

                    <div id="applicationFormSection">
                        <div class="section-heading">
                            <h2>Your author application</h2>
                            <p>
                                Complete the details below. Fields marked
                                with <span class="required-mark">*</span>
                                are required.
                            </p>
                        </div>

                        <form id="authorApplicationForm">
                            <div class="form-group">
                                <label for="display_name" class="form-label">
                                    Author Display Name
                                    <span class="required-mark">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="display_name"
                                    name="display_name"
                                    maxlength="100"
                                    required
                                    autocomplete="nickname"
                                    placeholder="e.g. MoonlitWriter">

                                <div class="field-help">
                                    This is the name readers will associate
                                    with your writing.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="bio" class="form-label">
                                    About You and Your Writing
                                    <span class="required-mark">*</span>
                                </label>

                                <textarea
                                    class="form-control"
                                    id="bio"
                                    name="bio"
                                    minlength="30"
                                    maxlength="5000"
                                    required
                                    placeholder="Tell us about yourself, your writing interests, and the stories you want to share."></textarea>

                                <div class="field-help">
                                    Write at least 30 characters, up to 5,000.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sample_work_url" class="form-label">
                                    Sample Work Link
                                    <span class="text-secondary fw-normal">
                                        (Optional)
                                    </span>
                                </label>

                                <input
                                    type="url"
                                    class="form-control"
                                    id="sample_work_url"
                                    name="sample_work_url"
                                    maxlength="255"
                                    placeholder="https://example.com/my-story">

                                <div class="field-help">
                                    Share a sample of your writing if you
                                    already have one published online.
                                </div>
                            </div>

                            <div class="notice">
                                <i class="bi bi-shield-check"></i>
                                <div>
                                    <strong class="d-block mb-1"
                                            style="color: #f5f3ff;">
                                        Your application will be reviewed
                                    </strong>
                                    Submitting this form does not
                                    automatically grant author access.
                                    An administrator must review your
                                    application first.
                                </div>
                            </div>

                            <button
                                type="submit"
                                id="submitApplication"
                                class="submit-button">
                                <i class="bi bi-send"></i>
                                Submit Application
                            </button>
                        </form>
                    </div>

                <?php endif; ?>

            </div>
        </section>
    </div>
</main>

<script>
(() => {
    const form = document.getElementById('authorApplicationForm');

    if (!form) return;

    const alertBox = document.getElementById('applicationAlert');
    const statusBox = document.getElementById('applicationStatus');
    const statusTitle = document.getElementById('statusTitle');
    const statusMessage = document.getElementById('statusMessage');
    const formSection = document.getElementById('applicationFormSection');
    const submitButton = document.getElementById('submitApplication');

    const apiUrl = '/novelverse/backend/api/author_applications.php';

    function showAlert(message, type = 'danger') {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
    }

    function showStatus(status, title, message) {
        statusBox.className = `status-panel ${status}`;
        statusTitle.textContent = title;
        statusMessage.textContent = message;
    }

    async function request(action, options = {}) {
        const response = await fetch(
            `${apiUrl}?action=${encodeURIComponent(action)}`,
            {
                credentials: 'same-origin',
                ...options
            }
        );

        let result;

        try {
            result = await response.json();
        } catch {
            throw new Error(
                'The server returned an unexpected response. Please try again.'
            );
        }

        if (!response.ok) {
            throw new Error(
                result.error || 'Something went wrong. Please try again.'
            );
        }

        return result;
    }

    async function loadApplicationStatus() {
        try {
            const result = await request('mine');
            const application = result.data;

            if (!application) return;

            const status = String(application.status || '').toLowerCase();

            if (status === 'pending') {
                showStatus(
                    'pending',
                    'Application Pending',
                    'Your application has been submitted and is waiting for admin review.'
                );

                formSection.classList.add('d-none');
                statusBox.classList.remove('d-none');
            } else if (status === 'approved') {
                showStatus(
                    'approved',
                    'Application Approved',
                    'Congratulations! Your application was approved. Visit your author dashboard to get started.'
                );

                formSection.classList.add('d-none');
                statusBox.classList.remove('d-none');
            } else if (status === 'rejected') {
                const note = application.review_note
                    ? '\nAdmin note: ' + application.review_note
                    : '\nYou may submit a new application with updated details.';

                showStatus(
                    'rejected',
                    'Application Not Approved',
                    'Your previous application was rejected.' + note
                );

                statusBox.classList.remove('d-none');
            }
        } catch (error) {
            console.error('Could not load application status:', error);
            showAlert(
                'We could not load your application status. You can retry by refreshing the page.',
                'warning'
            );
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        alertBox.classList.add('d-none');
        submitButton.disabled = true;
        submitButton.innerHTML =
            '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Submitting...';

        const payload = {
            display_name: form.elements.display_name.value.trim(),
            bio: form.elements.bio.value.trim(),
            sample_work_url: form.elements.sample_work_url.value.trim()
        };

        try {
            const result = await request('apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            showAlert(
                result.data?.message ||
                    'Your application has been submitted for review.',
                'success'
            );

            form.reset();
            formSection.classList.add('d-none');

            showStatus(
                'pending',
                'Application Pending',
                'Your application has been submitted and is waiting for admin review.'
            );

            statusBox.classList.remove('d-none');
        } catch (error) {
            showAlert(error.message);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML =
                '<i class="bi bi-send"></i> Submit Application';
        }
    });

    loadApplicationStatus();
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

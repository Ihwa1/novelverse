
<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>

/* ==========================================
   NOVELVERSE AUTHOR APPLICATION
   Modern dark theme with violet accents
========================================== */

.author-application-page {
    --author-bg: #11121a;
    --author-card: #1c1d29;
    --author-input: #141520;
    --author-border: #39394c;
    --author-text: #f5f3ff;
    --author-muted: #c1bfd1;
    --author-accent: #8b5cf6;
    --author-accent-hover: #7c3aed;

    max-width: 900px;
    margin: 48px auto;
    padding: 0 22px 60px;
    color: var(--author-text);
}

/* Main application card */
.author-application-card {
    position: relative;
    overflow: hidden;
    padding: 38px;
    background:
        radial-gradient(
            ellipse at top right,
            rgba(139, 92, 246, 0.12),
            transparent 45%
        ),
        var(--author-card);
    border: 1px solid var(--author-border);
    border-radius: 22px;
    box-shadow: 0 20px 55px rgba(0, 0, 0, 0.28);
}

/* Top decorative accent */
.author-application-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(
        90deg,
        #8b5cf6,
        #a78bfa,
        #6366f1
    );
}

/* Page heading */
.author-application-heading {
    margin-bottom: 34px;
}

.author-application-heading h1 {
    color: var(--author-text);
    font-size: clamp(1.8rem, 4vw, 2.55rem);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.8px;
    margin-bottom: 14px;
}

.author-application-heading p {
    max-width: 650px;
    color: var(--author-muted);
    font-size: 1rem;
    line-height: 1.8;
    margin-bottom: 0;
}

/* Form labels */
.author-form-label {
    display: block;
    color: var(--author-text);
    font-size: 0.95rem;
    font-weight: 650;
    margin-bottom: 10px;
}

/* Input fields */
.author-form-control {
    display: block;
    width: 100%;
    box-sizing: border-box;
    padding: 14px 16px;

    color: var(--author-text);
    background: var(--author-input);

    border: 1px solid #49485d;
    border-radius: 12px;

    font: inherit;
    font-size: 0.96rem;
    line-height: 1.6;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}

.author-form-control::placeholder {
    color: #aaa7bd;
    opacity: 1;
}

.author-form-control:hover {
    border-color: #77718f;
}

.author-form-control:focus {
    outline: none;
    color: #ffffff;
    background: #171825;
    border-color: var(--author-accent);
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.18);
}

textarea.author-form-control {
    min-height: 155px;
    resize: vertical;
}

/* Helper text */
.author-form-help {
    display: block;
    color: var(--author-muted);
    font-size: 0.85rem;
    line-height: 1.6;
    margin-top: 9px;
}

/* Submission button */
.author-submit-button {
    display: block;
    width: 100%;
    padding: 15px 20px;

    color: #ffffff;
    background: linear-gradient(135deg, #8b5cf6, #7045d6);

    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;

    font: inherit;
    font-size: 1rem;
    font-weight: 750;

    cursor: pointer;

    box-shadow: 0 7px 20px rgba(124, 58, 237, 0.22);

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}

.author-submit-button:hover {
    color: #ffffff;
    background: linear-gradient(135deg, #9b72ff, #7c3aed);
    box-shadow: 0 10px 26px rgba(124, 58, 237, 0.32);
    transform: translateY(-2px);
}

.author-submit-button:focus-visible {
    outline: 3px solid #c4b5fd;
    outline-offset: 4px;
}

.author-submit-button:active {
    transform: translateY(0);
}

.author-submit-button:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

/* Application status and information panels */
.author-status-panel {
    padding: 22px;
    margin-bottom: 26px;

    color: var(--author-text);
    background: #242432;

    border: 1px solid var(--author-border);
    border-radius: 14px;
}

.author-status-panel h3 {
    color: var(--author-text);
    font-size: 1.08rem;
    font-weight: 750;
    margin-bottom: 10px;
}

.author-status-panel p {
    color: var(--author-muted);
    font-size: 0.94rem;
    line-height: 1.75;
    overflow-wrap: anywhere;
}

.author-status-pending {
    border-left: 4px solid #fbbf24;
}

.author-status-approved {
    border-left: 4px solid #34d399;
}

.author-status-rejected {
    border-left: 4px solid #fb7185;
}

/* Feedback messages */
.author-application-message {
    display: none;
    margin-top: 18px;
    padding: 15px 17px;
    border-radius: 12px;
    font-size: 0.94rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.author-application-message.error {
    display: block;
    color: #fecaca;
    background: #351c28;
    border: 1px solid #793344;
}

.author-application-message.success {
    display: block;
    color: #bbf7d0;
    background: #173528;
    border: 1px solid #286747;
}

/* Consistent form spacing */
.author-application-card .mb-4 {
    margin-bottom: 26px !important;
}

/* Responsive layout */
@media (max-width: 768px) {
    .author-application-page {
        margin-top: 28px;
        padding: 0 16px 40px;
    }

    .author-application-card {
        padding: 30px 24px;
        border-radius: 18px;
    }
}

@media (max-width: 480px) {
    .author-application-page {
        margin-top: 18px;
        padding: 0 12px 30px;
    }

    .author-application-card {
        padding: 27px 18px;
        border-radius: 15px;
    }

    .author-application-heading {
        margin-bottom: 27px;
    }

    .author-application-heading h1 {
        font-size: 1.8rem;
    }

    .author-form-control {
        padding: 13px;
    }
}
</style>

<main class="author-application-page">
    <section class="author-application-card">
        <div class="author-application-heading">
            <h1>Become a NovelVerse Author</h1>
            <p>
                Share your stories with the NovelVerse community.
                Complete this application so our administrators can
                review your request for Author access.
            </p>
        </div>

        <div id="authorLoading" role="status">
            Checking your account and application...
        </div>

        <div id="authorLoginRequired" hidden>
            <div class="author-status-panel">
                <h3>Login required</h3>
                <p>
                    Please log in to your NovelVerse Reader account
                    before submitting an Author application.
                </p>
            </div>
            <a href="index.php" class="btn btn-outline-primary">
                Return to NovelVerse
            </a>
        </div>

        <div id="authorAlreadyAuthor" hidden>
            <div class="author-status-panel author-status-approved">
                <h3>You already have Author access</h3>
                <p>Your account can already use the Author features.</p>
            </div>
        </div>

        <div id="authorApplicationStatus" hidden>
            <div id="authorStatusPanel" class="author-status-panel">
                <h3 id="authorStatusTitle"></h3>
                <p id="authorStatusDescription"></p>
                <p id="authorReviewNote" hidden style="margin-top: 12px;"></p>
            </div>

            <button
                type="button"
                id="authorResubmitButton"
                class="author-submit-button"
                hidden
            >
                Submit a New Application
            </button>
        </div>

        <form id="authorApplicationForm" hidden>
            <div class="mb-4">
                <label for="authorDisplayName" class="author-form-label">
                    Author Display Name
                </label>
                <input
                    type="text"
                    id="authorDisplayName"
                    name="display_name"
                    class="author-form-control"
                    maxlength="100"
                    placeholder="Enter the name readers will see"
                    required
                >
                <small class="author-form-help">
                    This is the name you want to use when publishing stories.
                </small>
            </div>

            <div class="mb-4">
                <label for="authorBio" class="author-form-label">
                    About You and Your Writing
                </label>
                <textarea
                    id="authorBio"
                    name="bio"
                    class="author-form-control"
                    rows="6"
                    minlength="30"
                    maxlength="5000"
                    placeholder="Tell us about yourself, your writing interests, and the stories you plan to publish."
                    required
                ></textarea>
                <small class="author-form-help">
                    Write at least 30 characters. Maximum 5,000 characters.
                </small>
            </div>

            <div class="mb-4">
                <label for="authorSampleUrl" class="author-form-label">
                    Writing Sample or Portfolio URL
                    <span style="font-weight: 400;">(Optional)</span>
                </label>
                <input
                    type="url"
                    id="authorSampleUrl"
                    name="sample_work_url"
                    class="author-form-control"
                    maxlength="255"
                    placeholder="https://example.com/my-writing"
                >
                <small class="author-form-help">
                    You can provide a link to your existing work or portfolio.
                </small>
            </div>

            <div class="author-status-panel">
                <h3>Before you submit</h3>
                <p>
                    Your application will initially be marked as Pending.
                    Submitting this form does not grant Author permissions.
                    An administrator must review your application first.
                </p>
            </div>

            <button
                type="submit"
                id="authorSubmitButton"
                class="author-submit-button"
            >
                Submit Author Application
            </button>

            <div
                id="authorApplicationMessage"
                class="author-application-message"
                role="status"
                aria-live="polite"
            ></div>
        </form>
    </section>
</main>

<script>
(() => {
    const API_URL = '../backend/api/author_application.php';
    const AUTH_URL = '../backend/api/auth.php';

    const loading = document.getElementById('authorLoading');
    const loginRequired = document.getElementById('authorLoginRequired');
    const alreadyAuthor = document.getElementById('authorAlreadyAuthor');
    const statusSection = document.getElementById('authorApplicationStatus');
    const statusPanel = document.getElementById('authorStatusPanel');
    const statusTitle = document.getElementById('authorStatusTitle');
    const statusDescription = document.getElementById('authorStatusDescription');
    const reviewNote = document.getElementById('authorReviewNote');
    const form = document.getElementById('authorApplicationForm');
    const submitButton = document.getElementById('authorSubmitButton');
    const messageBox = document.getElementById('authorApplicationMessage');
    const resubmitButton = document.getElementById('authorResubmitButton');

    let canApply = false;

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            credentials: 'include',
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...(options.headers || {})
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Something went wrong.');
        }

        return result;
    }

    function showMessage(message, type = 'error') {
        messageBox.textContent = message;
        messageBox.className = 'author-application-message ' + type;
    }

    function hideAllSections() {
        loginRequired.hidden = true;
        alreadyAuthor.hidden = true;
        statusSection.hidden = true;
        form.hidden = true;
    }

    function showApplicationStatus(application) {
        hideAllSections();
        statusSection.hidden = false;

        const status = application.status;

        statusPanel.className = 'author-status-panel';
        reviewNote.hidden = true;
        resubmitButton.hidden = true;

        if (status === 'pending') {
            statusPanel.classList.add('author-status-pending');
            statusTitle.textContent = 'Application Pending';
            statusDescription.textContent =
                'Your application has been received and is awaiting administrator review. You cannot publish as an Author until it is approved.';
        } else if (status === 'approved') {
            statusPanel.classList.add('author-status-approved');
            statusTitle.textContent = 'Application Approved';
            statusDescription.textContent =
                'Your application has been approved. If your account does not yet have Author access, please contact an administrator.';
        } else {
            statusPanel.classList.add('author-status-rejected');
            statusTitle.textContent = 'Application Not Approved';
            statusDescription.textContent =
                'Your previous application was not approved. You may review your details and submit a new application.';

            if (application.review_note) {
                reviewNote.textContent = 'Administrator feedback: ' + application.review_note;
                reviewNote.hidden = false;
            }

            resubmitButton.hidden = false;
        }
    }

    async function initializePage() {
        try {
            const meResult = await requestJson(AUTH_URL + '?action=me');
            const user = meResult.data;

            if (!user || !user.id) {
                hideAllSections();
                loginRequired.hidden = false;
                return;
            }

            if (Number(user.is_admin) === 1) {
                hideAllSections();
                alreadyAuthor.hidden = false;
                const title = alreadyAuthor.querySelector('h3');
                const description = alreadyAuthor.querySelector('p');
                title.textContent = 'Administrator account';
                description.textContent =
                    'You are logged in as an administrator. Review Reader applications through the admin approval workflow.';
                return;
            }

            if (user.role === 'author') {
                hideAllSections();
                alreadyAuthor.hidden = false;
                return;
            }

            const applicationResult = await requestJson(API_URL + '?action=mine');
            const application = applicationResult.data;

            if (application && application.status !== 'rejected') {
                showApplicationStatus(application);
                return;
            }

            hideAllSections();
            form.hidden = false;
            canApply = true;

            if (application && application.status === 'rejected') {
                showApplicationStatus(application);
                resubmitButton.hidden = false;
                resubmitButton.onclick = () => {
                    statusSection.hidden = true;
                    form.hidden = false;
                    form.reset();
                    messageBox.className = 'author-application-message';
                    canApply = true;
                };
            }

        } catch (error) {
            hideAllSections();
            loginRequired.hidden = false;
            const paragraph = loginRequired.querySelector('p');
            paragraph.textContent =
                error.message || 'Unable to verify your login. Please refresh and try again.';
        } finally {
            loading.hidden = true;
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!canApply) return;

        const displayName = document.getElementById('authorDisplayName').value.trim();
        const bio = document.getElementById('authorBio').value.trim();
        const sampleUrl = document.getElementById('authorSampleUrl').value.trim();

        if (!displayName || !bio) {
            showMessage('Please complete the required fields.');
            return;
        }

        if (bio.length < 30) {
            showMessage('Your bio must contain at least 30 characters.');
            return;
        }

        if (sampleUrl) {
            try {
                const parsedUrl = new URL(sampleUrl);
                if (!['http:', 'https:'].includes(parsedUrl.protocol)) {
                    showMessage('Please use an HTTP or HTTPS URL.');
                    return;
                }
            } catch {
                showMessage('Please enter a valid sample work URL.');
                return;
            }
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
        messageBox.className = 'author-application-message';

        try {
            const result = await requestJson(API_URL + '?action=apply', {
                method: 'POST',
                body: JSON.stringify({
                    display_name: displayName,
                    bio: bio,
                    sample_work_url: sampleUrl
                })
            });

            canApply = false;
            showMessage(
                result.data?.message || 'Your application was submitted successfully.',
                'success'
            );

            setTimeout(() => window.location.reload(), 1200);

        } catch (error) {
            showMessage(error.message || 'Unable to submit your application.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Submit Author Application';
        }
    });

    initializePage();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

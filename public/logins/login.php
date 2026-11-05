<?php
$pageTitle = 'Log in';
require_once __DIR__ . '/includes/header.php';

// Already logged in → go home
if (!empty($nv_user)) {
    header('Location: /public/index.php');
    exit;
}
?>

<main class="kb-auth-page">
  <div class="kb-auth-card">
    <a href="/public/index.php" class="kb-back">← Go Back</a>

    <div class="kb-auth-brand">
      <div class="kb-auth-logo"><i class="bi bi-book-half"></i></div>
      <h1>KOMIKBYTE</h1>
      <p class="kb-auth-sub">Welcome back — log in to keep reading.</p>
    </div>

    <div id="tabLogin">
      <label class="kb-label">Email or username</label>
      <div class="kb-input-wrap">
        <span class="kb-input-icon"><i class="bi bi-envelope"></i></span>
        <input type="text" id="liEmail" class="kb-input" placeholder="you@example.com" autocomplete="username">
      </div>

      <label class="kb-label">Password</label>
      <div class="kb-input-wrap">
        <span class="kb-input-icon"><i class="bi bi-lock"></i></span>
        <input type="password" id="liPass" class="kb-input" placeholder="Your password" autocomplete="current-password">
      </div>

      <button type="button" class="kb-btn-primary"
              onclick="login(document.getElementById('liEmail').value, document.getElementById('liPass').value)">
        Log in
      </button>
    </div>

    <p class="kb-switch">
      Don’t have an account?
      <a href="/public/register.php">Create one</a>
    </p>
  </div>
</main>

<style>
.kb-auth-page {
  min-height: calc(100vh - 120px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 16px;
  background:
    radial-gradient(ellipse at top, rgba(124, 58, 237, 0.15), transparent 55%),
    #0b0c12;
}
.kb-auth-card {
  width: 100%;
  max-width: 420px;
  background: #12141c;
  border: 1px solid rgba(139, 92, 246, 0.35);
  border-radius: 16px;
  padding: 28px 24px 24px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.45);
  position: relative;
}
.kb-back {
  display: inline-block;
  margin-bottom: 12px;
  color: #9ca3af;
  text-decoration: none;
  font-size: 0.9rem;
}
.kb-back:hover { color: #c4b5fd; }
.kb-auth-brand { text-align: center; margin-bottom: 20px; }
.kb-auth-logo { font-size: 1.75rem; color: #a78bfa; }
.kb-auth-brand h1 {
  margin: 6px 0 0;
  font-size: 1.5rem;
  font-weight: 800;
  letter-spacing: 0.06em;
  background: linear-gradient(90deg, #a78bfa, #7c3aed);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.kb-auth-sub { margin: 8px 0 0; color: #9ca3af; font-size: 0.9rem; }
.kb-label {
  display: block;
  font-size: 0.8rem;
  color: #c4b5fd;
  margin: 12px 0 6px;
  font-weight: 600;
}
.kb-input-wrap { position: relative; }
.kb-input-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: #a78bfa; pointer-events: none;
}
.kb-input {
  width: 100%;
  background: #1a1d27 !important;
  border: 1px solid rgba(139, 92, 246, 0.25) !important;
  border-radius: 10px !important;
  color: #fff !important;
  padding: 0.7rem 0.85rem 0.7rem 2.35rem !important;
}
.kb-input:focus {
  border-color: #8b5cf6 !important;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
}
.kb-btn-primary {
  width: 100%;
  margin-top: 18px;
  border: none;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(90deg, #8b5cf6, #6d28d9);
  cursor: pointer;
}
.kb-btn-primary:hover { filter: brightness(1.08); }
.kb-switch {
  text-align: center;
  margin: 18px 0 0;
  font-size: 0.85rem;
  color: #9ca3af;
}
.kb-switch a { color: #c4b5fd; font-weight: 600; text-decoration: none; }
.kb-switch a:hover { text-decoration: underline; }
</style>

<script src="/public/js/app.js?v=101"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
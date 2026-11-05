<footer class="nv-site-footer">
  <div class="nv-footer-inner">
    <a href="/public/index.php" class="nv-footer-brand">
      <i class="bi bi-book-half"></i>
      <span>KomikByte</span>
    </a>
    <p class="nv-footer-description">
      Manga, Manhwa &amp; Manhua — your next favorite comic starts here.
    </p>
    <div class="nv-footer-divider"></div>
    <small>
      © 2026 KomikByte. Manga data provided by MangaDex API.
    </small>
  </div>
</footer>

<?php if (empty($nv_user)): ?>
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content kb-auth-modal">
      <button type="button" class="kb-auth-close" data-bs-dismiss="modal" aria-label="Close">×</button>
      <div class="kb-auth-brand">
        <div class="kb-auth-logo"><i class="bi bi-book-half"></i></div>
        <h2>KOMIKBYTE</h2>
        <p class="kb-auth-sub">Log in or create an account to start reading.</p>
      </div>
      <ul class="nav nav-pills kb-auth-tabs justify-content-center mb-3" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="tabLoginBtn"
                  data-bs-toggle="pill" data-bs-target="#tabLogin"
                  type="button" role="tab">Log in</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tabRegBtn"
                  data-bs-toggle="pill" data-bs-target="#tabReg"
                  type="button" role="tab">Register</button>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="tabLogin" role="tabpanel">
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
          <p class="kb-switch">
            Don’t have an account?
            <a href="#" onclick="document.getElementById('tabRegBtn').click(); return false;">Create one</a>
          </p>
        </div>
        <div class="tab-pane fade" id="tabReg" role="tabpanel">
          <label class="kb-label">Username</label>
          <div class="kb-input-wrap">
            <span class="kb-input-icon"><i class="bi bi-person"></i></span>
            <input type="text" id="rgUser" class="kb-input" placeholder="username" autocomplete="username">
          </div>
          <label class="kb-label">Email address</label>
          <div class="kb-input-wrap">
            <span class="kb-input-icon"><i class="bi bi-envelope"></i></span>
            <input type="email" id="rgEmail" class="kb-input" placeholder="you@example.com" autocomplete="email">
          </div>
          <label class="kb-label">Password</label>
          <div class="kb-input-wrap">
            <span class="kb-input-icon"><i class="bi bi-lock"></i></span>
            <input type="password" id="rgPass" class="kb-input" placeholder="Min 6 characters" autocomplete="new-password">
          </div>
          <div id="registerMessage" class="alert d-none mb-3" role="alert" aria-live="polite"></div>
          <button type="button" class="kb-btn-primary"
                  onclick="register(document.getElementById('rgUser').value, document.getElementById('rgEmail').value, document.getElementById('rgPass').value)">
            Create Account
          </button>
          <p class="kb-switch">
            Already have an account?
            <a href="#" onclick="document.getElementById('tabLoginBtn').click(); return false;">Log in</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<style>
#authModal .modal-dialog { max-width: 420px; margin: 1.75rem auto; }
.kb-auth-modal {
  background: #12141c !important;
  border: 1px solid rgba(139, 92, 246, 0.35) !important;
  border-radius: 16px !important;
  padding: 28px 24px 22px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.55);
  position: relative;
  color: #e8e8ef;
}
.kb-auth-close {
  position: absolute; top: 10px; right: 14px;
  background: transparent; border: none; color: #9ca3af;
  font-size: 1.5rem; line-height: 1; cursor: pointer; z-index: 2;
}
.kb-auth-close:hover { color: #fff; }
.kb-auth-brand { text-align: center; margin-bottom: 18px; }
.kb-auth-logo { font-size: 1.75rem; color: #a78bfa; margin-bottom: 6px; }
.kb-auth-brand h2 {
  margin: 0; font-weight: 800; letter-spacing: 0.06em; font-size: 1.45rem;
  background: linear-gradient(90deg, #a78bfa, #7c3aed);
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.kb-auth-sub { margin: 8px 0 0; color: #9ca3af; font-size: 0.9rem; }
.kb-auth-tabs .nav-link {
  border-radius: 999px; color: #9ca3af; padding: 0.35rem 1.1rem;
  font-weight: 600; background: transparent; border: none;
}
.kb-auth-tabs .nav-link.active {
  background: linear-gradient(90deg, #7c3aed, #6d28d9) !important;
  color: #fff !important;
}
.kb-label {
  display: block; font-size: 0.8rem; color: #c4b5fd;
  margin: 12px 0 6px; font-weight: 600;
}
.kb-input-wrap { position: relative; margin-bottom: 4px; }
.kb-input-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: #a78bfa; font-size: 0.9rem; pointer-events: none;
}
.kb-input {
  width: 100%;
  background: #1a1d27 !important;
  border: 1px solid rgba(139, 92, 246, 0.25) !important;
  border-radius: 10px !important;
  color: #fff !important;
  padding: 0.7rem 0.85rem 0.7rem 2.35rem !important;
  outline: none;
}
.kb-input::placeholder { color: #6b7280; }
.kb-input:focus {
  border-color: #8b5cf6 !important;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
}
.kb-btn-primary {
  width: 100%; margin-top: 18px; border: none; border-radius: 10px;
  padding: 0.75rem 1rem; font-weight: 700; color: #fff;
  background: linear-gradient(90deg, #8b5cf6, #6d28d9); cursor: pointer;
}
.kb-btn-primary:hover { filter: brightness(1.08); transform: translateY(-1px); }
.kb-switch { text-align: center; margin: 16px 0 0; font-size: 0.85rem; color: #9ca3af; }
.kb-switch a { color: #c4b5fd; font-weight: 600; text-decoration: none; }
.kb-switch a:hover { text-decoration: underline; }

.nv-toast-action {
  display: inline-block; margin-top: 10px; padding: 7px 12px;
  border: 1px solid rgba(129, 140, 248, .5); border-radius: 8px;
  background: rgba(99, 102, 241, .15); color: #c7d2fe;
  font-size: 12px; font-weight: 600; cursor: pointer;
}
.nv-toast-container {
  position: absolute; top: 12px; left: 50%; transform: translateX(-50%);
  z-index: 10000; display: flex; flex-direction: column; gap: 10px;
  width: calc(100% - 32px); max-width: 380px; pointer-events: none;
}
.nv-toast {
  display: flex; align-items: flex-start; gap: 13px; padding: 17px;
  border: 1px solid rgba(255,255,255,.12); border-left: 4px solid #ef4444;
  border-radius: 14px; background: #111827; color: #f9fafb;
  box-shadow: 0 12px 35px rgba(0,0,0,.38); pointer-events: auto;
  animation: nvToastIn .3s ease-out;
}
.nv-toast.success { border-left-color: #22c55e; }
.nv-toast.warning { border-left-color: #f59e0b; }
.nv-toast-icon {
  display: grid; place-items: center; flex-shrink: 0;
  width: 34px; height: 34px; border-radius: 50%;
  background: rgba(239,68,68,.15); color: #f87171; font-size: 19px; font-weight: 700;
}
.nv-toast.success .nv-toast-icon { background: rgba(34,197,94,.15); color: #4ade80; }
.nv-toast.warning .nv-toast-icon { background: rgba(245,158,11,.15); color: #fbbf24; }
.nv-toast-content { flex: 1; min-width: 0; }
.nv-toast-title { margin: 0 0 4px; font-size: 14px; font-weight: 700; }
.nv-toast-message { margin: 0; color: #cbd5e1; font-size: 13px; line-height: 1.5; }
.nv-toast-close {
  padding: 0 2px; border: 0; background: transparent; color: #9ca3af;
  font-size: 21px; line-height: 1; cursor: pointer;
}
.nv-toast-close:hover { color: #fff; }
.nv-toast.nv-toast-out { animation: nvToastOut .25s ease-in forwards; }
@keyframes nvToastIn {
  from { opacity: 0; transform: translateX(20px); }
  to   { opacity: 1; transform: translateX(0); }
}
@keyframes nvToastOut { to { opacity: 0; transform: translateX(20px); } }

.nv-site-footer {
  margin-top: 70px; padding: 38px 24px 28px;
  background: #11121a; border-top: 1px solid #30263f;
  color: #c1bfd1; text-align: center;
}
.nv-footer-inner { max-width: 1100px; margin: 0 auto; }
.nv-footer-brand {
  display: inline-flex; align-items: center; gap: 10px;
  color: #f5f3ff; font-size: 22px; font-weight: 750; text-decoration: none;
}
.nv-footer-brand i { color: #a78bfa; }
.nv-footer-brand:hover { color: #c4b5fd; }
.nv-footer-description { margin: 10px 0 22px; color: #c1bfd1; font-size: 14px; }
.nv-footer-divider {
  width: 100%; max-width: 420px; height: 1px; margin: 0 auto 20px;
  background: linear-gradient(90deg, transparent, #7651b5, transparent);
}
.nv-footer-inner small { color: #a6a3b8; font-size: 12px; line-height: 1.7; }
@media (max-width: 576px) {
  .nv-site-footer { margin-top: 45px; padding: 30px 18px 24px; }
  .nv-footer-brand { font-size: 20px; }
}
</style>

<script>
(async function () {
  const el = document.getElementById('navCoinBalance');
  if (!el) return;
  if (typeof api !== 'function') {
    el.textContent = '0';
    return;
  }
  try {
    const res = await api('wallet.php?action=balance');
    el.textContent = (res.data && res.data.balance != null) ? res.data.balance : 0;
  } catch (e) {
    el.textContent = '0';
  }
})();
</script>

</body>
</html>
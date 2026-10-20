<footer class="nv-site-footer">
    <div class="nv-footer-inner">
        <a href="index.php" class="nv-footer-brand">
            <i class="bi bi-moon-stars-fill"></i>
            <span>NovelVerse</span>
        </a>

        <p class="nv-footer-description">
            Your next favorite story starts here.
        </p>

        <div class="nv-footer-divider"></div>

        <small>
            © 2026 NovelVerse. Manga data provided by MangaDex API.
        </small>
    </div>
</footer>
```

<!-- ===== AUTH MODAL (only shown when logged out) ===== -->
<?php if (!$nv_user): ?>
<div class="modal fade" id="authModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-header border-0">
        <h5 class="modal-title text-white"><i class="bi bi-moon-stars-fill me-2"></i>Welcome to NovelVerse</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-pills mb-3">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabLogin">Login</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabReg">Register</button></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="tabLogin">
            <input class="form-control mb-2" id="liEmail" placeholder="Email or username">
            <input class="form-control mb-3" id="liPass" type="password" placeholder="Password">
            <button class="btn btn-nv w-100" onclick="login(liEmail.value, liPass.value)">Login</button>
          </div>
          <div class="tab-pane fade" id="tabReg">
            <input class="form-control mb-2" id="rgUser" placeholder="Username">
            <input class="form-control mb-2" id="rgEmail" placeholder="Email">
            <input class="form-control mb-3" id="rgPass" type="password" placeholder="Password (min 6 chars)">
          <div id="registerMessage"
              class="alert d-none mb-3"
              role="alert"
              aria-live="polite"></div>

            <button class="btn btn-nv w-100"
                    onclick="register(rgUser.value, rgEmail.value, rgPass.value)">
                Create Account
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<style>  
.nv-toast-action {
    display: inline-block;
    margin-top: 10px;
    padding: 7px 12px;
    border: 1px solid rgba(129, 140, 248, .5);
    border-radius: 8px;
    background: rgba(99, 102, 241, .15);
    color: #c7d2fe;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s ease;
}

.nv-toast-action:hover {
    background: rgba(99, 102, 241, .3);
}

.nv-toast-container {
    position: absolute;
    top: 12px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: calc(100% - 32px);
    max-width: 380px;
    pointer-events: none;
}

.nv-toast {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 17px;
    border: 1px solid rgba(255,255,255,.12);
    border-left: 4px solid #ef4444;
    border-radius: 14px;
    background: #111827;
    color: #f9fafb;
    box-shadow: 0 12px 35px rgba(0,0,0,.38);
    font-family: inherit;
    pointer-events: auto;
    animation: nvToastIn .3s ease-out;
}

.nv-toast.success {
    border-left-color: #22c55e;
}

.nv-toast.warning {
    border-left-color: #f59e0b;
}

.nv-toast-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(239,68,68,.15);
    color: #f87171;
    font-size: 19px;
    font-weight: 700;
}

.nv-toast.success .nv-toast-icon {
    background: rgba(34,197,94,.15);
    color: #4ade80;
}

.nv-toast.warning .nv-toast-icon {
    background: rgba(245,158,11,.15);
    color: #fbbf24;
}

.nv-toast-content {
    flex: 1;
    min-width: 0;
}

.nv-toast-title {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 700;
}

.nv-toast-message {
    margin: 0;
    color: #cbd5e1;
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.nv-toast-close {
    padding: 0 2px;
    border: 0;
    background: transparent;
    color: #9ca3af;
    font-size: 21px;
    line-height: 1;
    cursor: pointer;
}

.nv-toast-close:hover {
    color: #fff;
}

.nv-toast.nv-toast-out {
    animation: nvToastOut .25s ease-in forwards;
}

@keyframes nvToastIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

@keyframes nvToastOut {
    to { opacity: 0; transform: translateX(20px); }
}

@media (max-width: 480px) {
    .nv-toast-container {
        top: 12px;
        right: 12px;
        width: calc(100vw - 24px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .nv-toast, .nv-toast.nv-toast-out {
        animation: none;
    }
}

/* NovelVerse modern footer */
.nv-site-footer {
    margin-top: 70px;
    padding: 38px 24px 28px;
    background: #11121a;
    border-top: 1px solid #30263f;
    color: #c1bfd1;
    text-align: center;
}

.nv-footer-inner {
    max-width: 1100px;
    margin: 0 auto;
}

.nv-footer-brand {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #f5f3ff;
    font-size: 22px;
    font-weight: 750;
    text-decoration: none;
}

.nv-footer-brand i {
    color: #a78bfa;
}

.nv-footer-brand:hover {
    color: #c4b5fd;
}

.nv-footer-description {
    margin: 10px 0 22px;
    color: #c1bfd1;
    font-size: 14px;
}

.nv-footer-divider {
    width: 100%;
    max-width: 420px;
    height: 1px;
    margin: 0 auto 20px;
    background: linear-gradient(
        90deg,
        transparent,
        #7651b5,
        transparent
    );
}

.nv-footer-inner small {
    color: #a6a3b8;
    font-size: 12px;
    line-height: 1.7;
}

@media (max-width: 576px) {
    .nv-site-footer {
        margin-top: 45px;
        padding: 30px 18px 24px;
    }

    .nv-footer-brand {
        font-size: 20px;
    }
}
</style>
</body>
</html>
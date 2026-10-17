<footer class="py-4 text-center mt-5">
  <small>NovelVerse © 2026 — Manga data via MangaDex API</small>
</footer>

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
            <!-- ✅ CHANGED: now also sends the selected role -->
            <button class="btn btn-nv w-100"
                    onclick="register(rgUser.value, rgEmail.value, rgPass.value, 'reader')">
                Create Account
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ✅ ADDED: makes the role cards highlight when clicked -->
<script>
document.addEventListener('click', function (e) {
  const card = e.target.closest('.role-card');
  if (!card) return;
  document.querySelectorAll('#rolePicker .role-card').forEach(c => c.classList.remove('selected'));
  card.classList.add('selected');
  card.querySelector('input[type=radio]').checked = true;
});
</script>
</body>
</html>
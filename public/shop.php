<?php
$pageTitle = 'Coin Shop';
require_once __DIR__ . '/includes/header.php';

if (empty($nv_user)) {
    header('Location: ' . ($BASE ?? '/public') . '/login.php');
    exit;
}
?>

<main class="container py-5" style="max-width:900px">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
      <i class="bi bi-cart3 me-2"></i>Coin Shop
    </h2>
    <div class="kb-balance-pill">
      <i class="bi bi-coin"></i>
      <span id="shopBalance">…</span> coins
    </div>
  </div>

  <p class="text-muted-nv mb-4">
    Buy coins to unlock chapters after chapter 20. (Demo mode — no real charge yet.)
  </p>

  <div class="row g-4" id="packGrid">
    <!-- packs rendered by JS -->
  </div>
</main>

<style>
.kb-balance-pill {
  background: #1a1d27;
  border: 1px solid rgba(245, 158, 11, 0.4);
  color: #fbbf24;
  padding: 0.4rem 1rem;
  border-radius: 999px;
  font-weight: 700;
}
.kb-pack-card {
  background: #12141c;
  border: 1px solid rgba(139, 92, 246, 0.25);
  border-radius: 14px;
  padding: 1.5rem;
  height: 100%;
  text-align: center;
}
.kb-pack-card.featured {
  border-color: #f59e0b;
  box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.3);
}
.kb-pack-coins {
  font-size: 2rem;
  font-weight: 800;
  color: #fbbf24;
}
.kb-pack-price {
  font-size: 1.25rem;
  color: #e5e7eb;
  margin: 0.5rem 0 1rem;
}
</style>

<script src="<?= $BASE ?? '/public' ?>/js/app.js?v=102"></script>
<script>
const PACKS = [
  { id: 'starter',  name: 'Starter',  coins: 50,  price: 25,  tag: null },
  { id: 'standard', name: 'Standard', coins: 120, price: 55,  tag: 'Popular' },
  { id: 'plus',     name: 'Plus',     coins: 250, price: 100, tag: null },
  { id: 'mega',     name: 'Mega',     coins: 600, price: 200, tag: 'Best value' },
];

async function refreshBalance() {
  try {
    const res = await api('wallet.php?action=balance');
    const b = res.data.balance;
    document.getElementById('shopBalance').textContent = b;
    const nav = document.getElementById('navCoinBalance');
    if (nav) nav.textContent = b;
  } catch (e) {
    document.getElementById('shopBalance').textContent = '0';
  }
}

function renderPacks() {
  document.getElementById('packGrid').innerHTML = PACKS.map(p => `
    <div class="col-6 col-md-3">
      <div class="kb-pack-card ${p.tag ? 'featured' : ''}">
        ${p.tag ? `<div class="badge bg-warning text-dark mb-2">${p.tag}</div>` : '<div class="mb-2">&nbsp;</div>'}
        <div class="kb-pack-coins"><i class="bi bi-coin"></i> ${p.coins}</div>
        <div class="text-muted-nv small">coins</div>
        <div class="kb-pack-price">₱${p.price}</div>
        <button class="btn btn-nv w-100" onclick="buyPack('${p.id}')">
          <i class="bi bi-cart-plus me-1"></i>Buy
        </button>
      </div>
    </div>
  `).join('');
}

async function buyPack(packId) {
  if (!confirm('Demo purchase — add coins to your wallet?')) return;
  try {
    const res = await api('wallet.php?action=buy', {
      method: 'POST',
      body: JSON.stringify({ pack: packId })
    });
    alert(res.data.message + '\\nNew balance: ' + res.data.balance);
    refreshBalance();
  } catch (e) {
    alert(e.message || 'Purchase failed');
  }
}

renderPacks();
refreshBalance();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
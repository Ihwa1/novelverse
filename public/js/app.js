const API = 'http://localhost/novelverse/backend/api';

const TYPE_BADGE = {
  manga:  { label: 'MANGA',  color: '#e63946' },
  manhwa: { label: 'MANHWA', color: '#2a9d8f' },
  manhua: { label: 'MANHUA', color: '#d4a017' },
  novel:  { label: 'NOVEL',  color: '#7b5cbf' }
};

async function api(path, opts = {}) {
  const res = await fetch(`${API}/${path}`, {
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    ...opts
  });
  const json = await res.json();
  if (!res.ok) throw Object.assign(new Error(json.error || 'Request failed'), { json, status: res.status });
  return json;
}

async function login(email, password) {
  await api('auth.php?action=login', { method: 'POST', body: JSON.stringify({ email, password }) });
  location.reload();
}

// ✅ CHANGED: now accepts a role (defaults to 'reader' if not passed)
async function register(username, email, password, role = 'reader') {
  await api('auth.php?action=register', {
    method: 'POST',
    body: JSON.stringify({ username, email, password, role })
  });
  location.reload();
}

// ✅ ADDED: track reading progress (call this from the reader page)
async function trackProgress(seriesId, chapterId) {
  try {
    await api('progress.php', {
      method: 'POST',
      body: JSON.stringify({ series_id: seriesId, chapter_id: chapterId })
    });
  } catch (e) {
    // Silent fail — guests aren't logged in, that's fine
  }
}

async function buyNovel(seriesId, method) {
  const res = await api('payments.php?action=create', {
    method: 'POST',
    body: JSON.stringify({ series_id: seriesId, method })
  });
  if (res.data.checkout_url) {
    location.href = res.data.checkout_url;
  } else {
    alert('Card payment intent created: ' + res.data.intent_id);
  }
}

function seriesCard(s) {
  const badge = TYPE_BADGE[s.type] || TYPE_BADGE.manga;
  return `
  <div class="col-6 col-md-4 col-lg-3">
    <a class="nv-card h-100" href="${s.type === 'novel' ? 'novel' : 'series'}.php?slug=${s.slug}">
      <div class="card-cover">
        <img src="${s.cover_url || 'https://via.placeholder.com/300x400/0a1128/5b9bff?text=No+Cover'}" loading="lazy" alt="">
        <span class="type-badge ${s.type}">${badge.label}</span>
      </div>
      <div class="card-body">
        <h3>${s.title}</h3>
        <p>${s.status || ''}${s.is_novel ? ' • ₱' + s.novel_price : ''}</p>
      </div>
    </a>
  </div>`;
}

function goSearch(e) {
  e.preventDefault();
  location.href = 'browse.php?q=' + encodeURIComponent(document.getElementById('navSearch').value);
}

// ✅ CHANGED: Asura-style trending section
(async () => {
  const grid = document.getElementById('trendingGrid');
    grid.innerHTML = `
    <div class="az-loading">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3 mb-0">Loading trending manhwa...</p>
    </div>`;
    
  try {
    const res = await fetch('/novelverse/public/api/anilist.php?action=trending').then(r => {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    });

    if (!res.data || res.data.length === 0) {
      grid.innerHTML = '<p class="text-muted">No trending data right now.</p>';
      return;
    }

    // AniList scores are 0-100 → convert to Asura-style x.x / 10
    const toTen = s => (s ? (s / 10).toFixed(1) : null);
    const stars = s => {
      const full = Math.round((s || 0) / 20); // 5-star scale
      return '★'.repeat(full) + '☆'.repeat(5 - full);
    };

    grid.innerHTML = res.data.map(m => {
      const score = toTen(m.score);
      return `
      <a href="browse.php?q=${encodeURIComponent(m.title)}&type=manhwa" class="az-card">
        <div class="az-cover">
          <img src="${m.cover}" alt="${m.title}" loading="lazy">
        </div>
        <div class="az-title" title="${m.title}">${m.title}</div>
        <div class="az-sub">${(m.genres || []).slice(0, 2).join(', ') || '&nbsp;'}</div>
        <div class="az-rating">
          <span class="az-stars">${stars(m.score)}</span>
          <span class="az-score">${score ?? 'N/A'}</span>
        </div>
      </a>`;
    }).join('');
  } catch (e) {
    grid.innerHTML = '<p class="text-danger">Failed to load trending manhwa.</p>';
  }
})();
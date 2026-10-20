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
    email = email.trim();

    if (!email || !password) {
        showToast(
            'Please enter your username/email and password.',
            'warning',
            { target: 'tabLogin' }
        );
        return;
    }

    try {
        await api('auth.php?action=login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        location.reload();

    } catch (error) {
        console.error('Login error:', error);

        const isInvalidCredentials =
            error.message === 'Invalid credentials';

        showToast(
            isInvalidCredentials
                ? 'Incorrect username/email or password. Please try again.'
                : error.message || 'Unable to log in. Please try again.',
            'danger',
            {
                target: 'tabLogin',
                actionText: 'Create an account',
                onAction: () => {
                    document.querySelector(
                        '[data-bs-target="#tabReg"]'
                    )?.click();
                }
            }
        );
    }
}

// ✅ CHANGED: now accepts a role (defaults to 'reader' if not passed)
function showToast(message, type = 'danger', options = {}) {
    const targetId = options.target || 'tabReg';
    let container = document.getElementById('nvToastContainer');

    if (!container) {
        container = document.createElement('div');
        container.id = 'nvToastContainer';
        container.className = 'nv-toast-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'false');
    }

    const targetPane = document.getElementById(targetId);

    if (targetPane && container.parentElement !== targetPane) {
        targetPane.style.position = 'relative';
        targetPane.appendChild(container);
    }

    const isSuccess = type === 'success';
    const isWarning = type === 'warning';

    const toast = document.createElement('div');
    toast.className = `nv-toast ${type}`;

    const icon = document.createElement('div');
    icon.className = 'nv-toast-icon';
    icon.textContent = isSuccess ? '✓' : isWarning ? '!' : '×';

    const content = document.createElement('div');
    content.className = 'nv-toast-content';

    const heading = document.createElement('p');
    heading.className = 'nv-toast-title';
    heading.textContent = isSuccess
        ? 'Success'
        : isWarning ? 'Please check' : 'Login / Registration Error';

    const description = document.createElement('p');
    description.className = 'nv-toast-message';
    description.textContent = message;

    content.append(heading, description);

    // Optional action button, such as switching to Register.
    if (options.actionText && options.onAction) {
        const action = document.createElement('button');
        action.type = 'button';
        action.className = 'nv-toast-action';
        action.textContent = options.actionText;
        action.addEventListener('click', () => {
            dismiss();
            options.onAction();
        });
        content.appendChild(action);
    }

    const close = document.createElement('button');
    close.className = 'nv-toast-close';
    close.type = 'button';
    close.textContent = '×';
    close.setAttribute('aria-label', 'Dismiss notification');

    toast.append(icon, content, close);
    container.appendChild(toast);

    let dismissed = false;
    let timer;

    function dismiss() {
        if (dismissed) return;
        dismissed = true;
        clearTimeout(timer);

        toast.classList.add('nv-toast-out');
        toast.addEventListener('animationend', () => toast.remove(), {
            once: true
        });

        setTimeout(() => toast.remove(), 350);
    }

    close.addEventListener('click', dismiss);
    timer = setTimeout(dismiss, 5000);
}

async function register(username, email, password) {
    const message = document.getElementById('registerMessage');
    const showMessage = (text, type = 'danger') => {
        showToast(text, type);
    };

    // Normalize input
    username = username.trim();
    email = email.trim();

    // Clear previous message
    if (message) {
        message.textContent = '';
        message.className = 'alert d-none mb-3';
    }

    // Validate required fields
    if (!username || !email || !password) {
        showMessage('Please fill in all fields.');
        return;
    }

    // Validate username
    if (username.length < 3) {
        showMessage('Username must be at least 3 characters long.');
        return;
    }

    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        showMessage(
            'Username can only contain letters, numbers, and underscores.'
        );
        return;
    }

    // Validate email
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showMessage('Please enter a valid email address.');
        return;
    }

    // Validate password
    if (password.length < 6) {
        showMessage('Password must be at least 6 characters long.');
        return;
    }

    try {
        await api('auth.php?action=register', {
            method: 'POST',
            body: JSON.stringify({
                username,
                email,
                password
            })
        });

        showMessage('Account created successfully!', 'success');

        // Preserve the existing behavior: log in and reload after registration.
       setTimeout(() => {
      location.reload();
      }, 500);

    } catch (error) {
        console.error('Registration error:', error);

        showMessage(
            error.message || 'Registration failed. Please try again.'
        );
    }
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


// Trending manhwa section
(async () => {
  const grid = document.getElementById('trendingGrid');

  // Not every page has a trending section.
  // Skip this feature on pages such as the Admin Dashboard.
  if (!grid) return;

  grid.innerHTML = `
    <div class="az-loading">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3 mb-0">Loading trending manhwa...</p>
    </div>
  `;

  try {
    const response = await fetch(
      '/novelverse/public/api/anilist.php?action=trending'
    );

    if (!response.ok) {
      throw new Error('HTTP ' + response.status);
    }

    const res = await response.json();

    if (!res.data || res.data.length === 0) {
      grid.innerHTML =
        '<p class="text-muted">No trending data right now.</p>';
      return;
    }

    // Convert AniList scores from 0–100 to a 10-point scale.
    const toTen = score =>
      score ? (score / 10).toFixed(1) : null;

    const stars = score => {
      const full = Math.round((score || 0) / 20);
      return '★'.repeat(full) + '☆'.repeat(5 - full);
    };

    grid.innerHTML = res.data.map(m => {
      const score = toTen(m.score);

      return `
        <a href="browse.php?q=${encodeURIComponent(m.title)}&type=manhwa"
           class="az-card">
          <div class="az-cover">
            <img src="${m.cover}" alt="${m.title}" loading="lazy">
          </div>
          <div class="az-title" title="${m.title}">
            ${m.title}
          </div>
          <div class="az-sub">
            ${(m.genres || []).slice(0, 2).join(', ') || '&nbsp;'}
          </div>
          <div class="az-rating">
            <span class="az-stars">${stars(m.score)}</span>
            <span class="az-score">${score ?? 'N/A'}</span>
          </div>
        </a>
      `;
    }).join('');
  } catch (error) {
    console.error('Trending manhwa error:', error);
    grid.innerHTML =
      '<p class="text-danger">Failed to load trending manhwa.</p>';
  }
})();

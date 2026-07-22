/* app.js — CineList */
'use strict';

// ── Toast ──────────────────────────────────────────────────────
const toast = document.getElementById('toast');
let toastTimer;
function showToast(msg, isError = false) {
  toast.textContent = msg;
  toast.className   = 'show' + (isError ? ' error' : '');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { toast.className = ''; }, 2800);
}

// ── Fetch helper ───────────────────────────────────────────────
async function post(data) {
  const fd = new FormData();
  Object.entries(data).forEach(([k, v]) => fd.append(k, v));
  const r   = await fetch('actions.php', { method: 'POST', body: fd });
  const res = await r.json();
  if (res.redirect) { window.location.href = res.redirect; return res; }
  return res;
}
async function get(params) {
  const url = 'actions.php?' + new URLSearchParams({ action: 'list', ...params });
  const r   = await fetch(url);
  const res = await r.json();
  if (res.redirect) { window.location.href = res.redirect; return res; }
  return res;
}

// ── State ──────────────────────────────────────────────────────
let currentFilter = 'tous';
let currentSort   = 'created_at';
let searchQuery   = '';
let searchTimer;

// ── Render films ───────────────────────────────────────────────
const grid = document.getElementById('films-grid');

function statusLabel(s) {
  return { a_voir: '🕐 À voir', en_cours: '▶ En cours', vu: '✓ Vu' }[s] || s;
}

function starsHTML(filmId, note) {
  return [1,2,3,4,5].map(n =>
    `<span class="star ${note >= n ? 'lit' : ''}" data-id="${filmId}" data-note="${n}" title="${n} étoile${n>1?'s':''}">★</span>`
  ).join('');
}

function filmCardHTML(f) {
  const poster = f.affiche_url
    ? `<img src="${escHtml(f.affiche_url)}" alt="${escHtml(f.titre)}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=no-img><span>🎬</span></div>'">`
    : `<div class="no-img"><span>🎬</span></div>`;

  const commentText = f.commentaire
    ? escHtml(f.commentaire)
    : '<em>Ajouter un commentaire…</em>';

  return `
    <div class="film-card" data-id="${f.id}">
      <div class="card-poster">${poster}</div>
      <div class="card-body">
        <div class="card-title">${escHtml(f.titre)}</div>
        <div class="card-meta">
          ${f.realisateur ? `<span>${escHtml(f.realisateur)}</span>` : ''}
          ${f.annee       ? `<span>${f.annee}</span>`                : ''}
          ${f.genre       ? `<span class="genre-badge">${escHtml(f.genre)}</span>` : ''}
        </div>
        <span class="status-badge status-${f.statut}">${statusLabel(f.statut)}</span>
        <div class="stars" data-id="${f.id}">${starsHTML(f.id, f.note)}</div>

        <div class="card-actions">
          <select class="status-select" data-id="${f.id}">
            <option value="a_voir"   ${f.statut==='a_voir'   ?'selected':''}>🕐 À voir</option>
            <option value="en_cours" ${f.statut==='en_cours' ?'selected':''}>▶ En cours</option>
            <option value="vu"       ${f.statut==='vu'       ?'selected':''}>✓ Vu</option>
          </select>
          <button class="btn btn-danger" data-action="delete" data-id="${f.id}" title="Supprimer">✕</button>
        </div>

        <div class="card-comment" data-id="${f.id}">${commentText}</div>
        <div class="comment-edit" id="ce-${f.id}">
          <textarea placeholder="Ton avis…" rows="3">${f.commentaire ? escHtml(f.commentaire) : ''}</textarea>
          <div class="comment-edit-btns">
            <button class="btn btn-sm btn-primary" style="flex:1" data-action="save-comment" data-id="${f.id}">Sauvegarder</button>
            <button class="btn btn-sm" style="background:var(--bg3);color:var(--muted)" data-action="cancel-comment" data-id="${f.id}">Annuler</button>
          </div>
        </div>
      </div>
    </div>`;
}

function escHtml(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function loadFilms() {
  grid.innerHTML = '<div class="loader"><div class="spinner"></div></div>';
  const data = await get({ filtre: currentFilter, q: searchQuery, order: currentSort });
  if (!data.ok) { showToast('Erreur de chargement', true); return; }

  if (!data.films.length) {
    grid.innerHTML = `<div class="empty"><div class="emoji">🎬</div><p>Aucun film trouvé.</p></div>`;
    return;
  }
  grid.innerHTML = data.films.map(filmCardHTML).join('');
  updateStats(data.films);
}

function updateStats(films) {
  document.getElementById('stat-total').textContent = films.length;
  // stats globales → on refait un appel léger ou on calcule côté client depuis la réponse
}

async function reloadStats() {
  const data = await get({ filtre: 'tous' });
  if (!data.ok) return;
  const all  = data.films;
  document.getElementById('stat-total').textContent = all.length;
  document.getElementById('stat-vu').textContent    = all.filter(f => f.statut === 'vu').length;
  document.getElementById('stat-queue').textContent = all.filter(f => f.statut === 'a_voir').length;
}

// ── Event delegation ───────────────────────────────────────────
grid.addEventListener('click', async (e) => {
  const el = e.target;

  // Star rating
  if (el.classList.contains('star')) {
    const id   = el.dataset.id;
    const note = el.dataset.note;
    const res  = await post({ action: 'rate', id, note });
    if (res.ok) { showToast(`Note : ${note} ★`); loadFilms(); }
    else showToast(res.msg, true);
    return;
  }

  // Delete
  if (el.dataset.action === 'delete') {
    if (!confirm('Supprimer ce film ?')) return;
    const res = await post({ action: 'delete', id: el.dataset.id });
    if (res.ok) { showToast('Film supprimé.'); loadFilms(); reloadStats(); }
    else showToast(res.msg, true);
    return;
  }

  // Toggle comment edit
  if (el.classList.contains('card-comment')) {
    const id = el.dataset.id;
    const ce = document.getElementById(`ce-${id}`);
    ce.style.display = ce.style.display === 'flex' ? 'none' : 'flex';
    if (ce.style.display === 'flex') ce.querySelector('textarea').focus();
    return;
  }

  // Save comment
  if (el.dataset.action === 'save-comment') {
    const id  = el.dataset.id;
    const txt = document.getElementById(`ce-${id}`).querySelector('textarea').value;
    const res = await post({ action: 'comment', id, commentaire: txt });
    if (res.ok) { showToast('Commentaire sauvegardé.'); loadFilms(); }
    else showToast(res.msg, true);
    return;
  }

  // Cancel comment
  if (el.dataset.action === 'cancel-comment') {
    const ce = document.getElementById(`ce-${el.dataset.id}`);
    ce.style.display = 'none';
    return;
  }
});

// Status change
grid.addEventListener('change', async (e) => {
  if (!e.target.classList.contains('status-select')) return;
  const id     = e.target.dataset.id;
  const statut = e.target.value;
  const res    = await post({ action: 'update_statut', id, statut });
  if (res.ok) { showToast('Statut mis à jour.'); loadFilms(); reloadStats(); }
  else showToast(res.msg, true);
});

// Star hover preview
grid.addEventListener('mouseover', (e) => {
  if (!e.target.classList.contains('star')) return;
  const note   = parseInt(e.target.dataset.note);
  const id     = e.target.dataset.id;
  document.querySelectorAll(`.star[data-id="${id}"]`).forEach((s, i) => {
    s.style.color = i < note ? 'var(--accent)' : 'var(--border)';
  });
});
grid.addEventListener('mouseout', (e) => {
  if (!e.target.classList.contains('star')) return;
  const id = e.target.dataset.id;
  const card = e.target.closest('.film-card');
  // restore from data
  loadCurrentStars(id, card);
});

function loadCurrentStars(id, card) {
  const litCount = card.querySelectorAll(`.star[data-id="${id}"].lit`).length;
  card.querySelectorAll(`.star[data-id="${id}"]`).forEach((s, i) => {
    s.style.color = i < litCount ? 'var(--accent)' : '';
  });
}

// ── Add form ───────────────────────────────────────────────────
document.getElementById('add-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd   = new FormData(e.target);
  const data = Object.fromEntries(fd.entries());
  data.action = 'add';
  const res = await post(data);
  if (res.ok) {
    showToast('Film ajouté ! 🎬');
    e.target.reset();
    loadFilms();
    reloadStats();
  } else {
    showToast(res.msg, true);
  }
});

// ── Filter tabs ────────────────────────────────────────────────
document.querySelectorAll('.filter-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter = btn.dataset.filter;
    loadFilms();
  });
});

// ── Sort ───────────────────────────────────────────────────────
document.getElementById('sort-select').addEventListener('change', (e) => {
  currentSort = e.target.value;
  loadFilms();
});

// ── Search ─────────────────────────────────────────────────────
document.getElementById('search').addEventListener('input', (e) => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    searchQuery = e.target.value.trim();
    loadFilms();
  }, 350);
});

// ── Init ───────────────────────────────────────────────────────
loadFilms();
reloadStats();

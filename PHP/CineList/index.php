<?php
// index.php — CineList
require_once 'auth.php';
$user = requireLogin();   // redirige vers login.php si non connecté

$pdo = getPDO();
$uid = $user['id'];

// Stats propres à l'utilisateur connecté
$stats = $pdo->prepare('
    SELECT
        COUNT(*) AS total,
        SUM(statut = "vu") AS vu,
        SUM(statut = "a_voir") AS a_voir,
        ROUND(AVG(note), 1) AS avg_note
    FROM films WHERE user_id = :uid
');
$stats->execute([':uid' => $uid]);
$stats = $stats->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CineList — Ma Watchlist</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div>
    <h1>CineList</h1>
    <p class="subtitle">Ta collection cinématographique personnelle</p>
  </div>
  <div class="user-bar">
    <span class="user-name">👤 <?= htmlspecialchars($user['username']) ?></span>
    <a href="logout.php" class="btn-logout" onclick="return confirm('Se déconnecter ?')">Déconnexion</a>
  </div>
  <div class="stats-bar">
    <div class="stat">
      <div class="num" id="stat-total"><?= $stats['total'] ?></div>
      <div class="lbl">Films</div>
    </div>
    <div class="stat">
      <div class="num" id="stat-vu"><?= $stats['vu'] ?? 0 ?></div>
      <div class="lbl">Vus</div>
    </div>
    <div class="stat">
      <div class="num" id="stat-queue"><?= $stats['a_voir'] ?? 0 ?></div>
      <div class="lbl">À voir</div>
    </div>
    <?php if ($stats['avg_note']): ?>
    <div class="stat">
      <div class="num"><?= $stats['avg_note'] ?> ★</div>
      <div class="lbl">Moy.</div>
    </div>
    <?php endif; ?>
  </div>
</header>

<div class="container">
  <div class="layout">

    <!-- ── Formulaire d'ajout ──────────────────────────── -->
    <aside>
      <div class="panel">
        <h2>+ Ajouter un film</h2>
        <form id="add-form" autocomplete="off" enctype="multipart/form-data">

          <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" placeholder="Ex: Interstellar" required>
          </div>

          <div class="form-group">
            <label for="realisateur">Réalisateur</label>
            <input type="text" id="realisateur" name="realisateur" placeholder="Ex: Christopher Nolan">
          </div>

          <div class="form-group">
            <label for="annee">Année</label>
            <input type="number" id="annee" name="annee" placeholder="Ex: 2014" min="1888" max="2099">
          </div>

          <div class="form-group">
            <label for="genre">Genre</label>
            <select id="genre" name="genre">
              <option value="">— Choisir —</option>
              <option>Sci-Fi</option>
              <option>Thriller</option>
              <option>Drame</option>
              <option>Action</option>
              <option>Comédie</option>
              <option>Horreur</option>
              <option>Animation</option>
              <option>Documentaire</option>
              <option>Romance</option>
              <option>Biopic</option>
              <option>Crime</option>
              <option>Fantaisie</option>
              <option>Guerre</option>
              <option>Western</option>
            </select>
          </div>

          <div class="form-group">
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
              <option value="a_voir">🕐 À voir</option>
              <option value="en_cours">▶ En cours</option>
              <option value="vu">✓ Vu</option>
            </select>
          </div>

          <div class="form-group">
            <label for="affiche">Affiche (jpg/png/webp, max 3 Mo)</label>
            <input type="file" id="affiche" name="affiche" accept="image/jpeg,image/png,image/webp,image/gif">
          </div>

          <button type="submit" class="btn btn-primary">Ajouter à la liste</button>
        </form>
      </div>
    </aside>

    <!-- ── Liste des films ────────────────────────────── -->
    <main>
      <div class="toolbar">
        <div class="search-wrap">
          <span class="ico">🔍</span>
          <input type="search" id="search" placeholder="Rechercher un film, réalisateur…">
        </div>

        <div class="filter-tabs">
          <button class="filter-tab active" data-filter="tous">Tous</button>
          <button class="filter-tab" data-filter="a_voir">À voir</button>
          <button class="filter-tab" data-filter="en_cours">En cours</button>
          <button class="filter-tab" data-filter="vu">Vus</button>
        </div>

        <select id="sort-select" class="sort-select" title="Trier par">
          <option value="created_at">Plus récents</option>
          <option value="titre">Titre A→Z</option>
          <option value="annee">Année</option>
          <option value="note">Note</option>
        </select>
      </div>

      <div id="films-grid">
        <!-- Rempli par app.js -->
      </div>
    </main>

  </div>
</div>

<div id="toast"></div>
<input type="hidden" id="current-user-id" value="<?= $uid ?>">
<script src="app.js"></script>
</body>
</html>

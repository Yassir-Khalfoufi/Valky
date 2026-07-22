<?php require 'app/views/layout/header.php'; ?>
<div class="page-header">
  <h1>All Films</h1>
  <form method="GET" action="/cinema/movies" class="search-form">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search title or director...">
    <button type="submit">Search</button>
  </form>
</div>
<div class="movies-grid">
  <?php foreach ($movies as $m): ?>
  <a href="/cinema/movies/show/<?= $m['id'] ?>" class="movie-card">
    <div class="movie-poster"><?= htmlspecialchars(strtoupper(substr($m['title'], 0, 2))) ?></div>
    <div class="movie-info">
      <h3><?= htmlspecialchars($m['title']) ?></h3>
      <p><?= $m['year'] ?> · <?= htmlspecialchars($m['genre']) ?></p>
      <p class="director"><?= htmlspecialchars($m['director']) ?></p>
    </div>
  </a>
  <?php endforeach; ?>
  <?php if (!$movies): ?><p class="muted">No films found.</p><?php endif; ?>
</div>
<?php require 'app/views/layout/footer.php'; ?>

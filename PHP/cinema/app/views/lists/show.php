<?php require 'app/views/layout/header.php'; ?>
<div class="page-header">
  <h1><?= htmlspecialchars($list['name']) ?></h1>
  <?php if ($list['description']): ?><p class="muted"><?= htmlspecialchars($list['description']) ?></p><?php endif; ?>
</div>
<div class="movies-grid">
  <?php foreach ($movies as $m): ?>
  <a href="/cinema/movies/show/<?= $m['id'] ?>" class="movie-card">
    <div class="movie-poster"><?= htmlspecialchars(strtoupper(substr($m['title'], 0, 2))) ?></div>
    <div class="movie-info">
      <h3><?= htmlspecialchars($m['title']) ?></h3>
      <p><?= $m['year'] ?> · <?= htmlspecialchars($m['genre']) ?></p>
    </div>
  </a>
  <?php endforeach; ?>
  <?php if (!$movies): ?><p class="muted">No films in this list yet.</p><?php endif; ?>
</div>
<div class="add-movie-form">
  <h3>Add a film by ID</h3>
  <form method="POST" action="/cinema/lists/addMovie/<?= $list['id'] ?>">
    <input type="number" name="movie_id" placeholder="Movie ID" required>
    <button type="submit">Add</button>
  </form>
</div>
<?php require 'app/views/layout/footer.php'; ?>

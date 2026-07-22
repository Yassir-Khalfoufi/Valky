<?php require 'app/views/layout/header.php'; ?>
<div class="movie-detail">
  <div class="movie-hero">
    <div class="movie-poster-lg"><?= htmlspecialchars(strtoupper(substr($movie['title'], 0, 2))) ?></div>
    <div class="movie-meta">
      <h1><?= htmlspecialchars($movie['title']) ?></h1>
      <p class="meta-line"><?= $movie['year'] ?> · <?= htmlspecialchars($movie['genre']) ?> · Dir. <?= htmlspecialchars($movie['director']) ?></p>
      <?php if ($avg): ?><p class="avg-rating">⭐ <?= $avg ?> / 5</p><?php endif; ?>
      <p><?= htmlspecialchars($movie['description']) ?></p>

      <?php if (isset($_SESSION['user'])): ?>
      <form method="POST" action="/cinema/movies/status/<?= $movie['id'] ?>" class="status-form">
        <select name="status">
          <option value="watchlist" <?= ($userMovie['status'] ?? '') === 'watchlist' ? 'selected' : '' ?>>📋 Watchlist</option>
          <option value="watched"   <?= ($userMovie['status'] ?? '') === 'watched'   ? 'selected' : '' ?>>✅ Watched</option>
        </select>
        <select name="rating">
          <option value="">No rating</option>
          <?php for ($i = 1; $i <= 5; $i++): ?>
          <option value="<?= $i ?>" <?= ($userMovie['rating'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?> ★</option>
          <?php endfor; ?>
        </select>
        <button type="submit">Save</button>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <section class="reviews-section">
    <h2>Reviews</h2>
    <?php if (isset($_SESSION['user'])): ?>
    <form method="POST" action="/cinema/review/create/<?= $movie['id'] ?>" class="review-form">
      <textarea name="body" placeholder="Write your review..." required rows="3"></textarea>
      <button type="submit">Post Review</button>
    </form>
    <?php endif; ?>

    <?php foreach ($reviews as $r): ?>
    <div class="review-card">
      <div class="review-header">
        <strong><?= htmlspecialchars($r['username']) ?></strong>
        <span><?= date('M j, Y', strtotime($r['created_at'])) ?></span>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $r['user_id']): ?>
        <form method="POST" action="/cinema/review/delete/<?= $r['id'] ?>">
          <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
          <button type="submit" class="btn-del">✕</button>
        </form>
        <?php endif; ?>
      </div>
      <p><?= nl2br(htmlspecialchars($r['body'])) ?></p>
    </div>
    <?php endforeach; ?>
    <?php if (!$reviews): ?><p class="muted">No reviews yet.</p><?php endif; ?>
  </section>
</div>
<?php require 'app/views/layout/footer.php'; ?>

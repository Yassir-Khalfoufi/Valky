<?php require 'app/views/layout/header.php'; ?>
<div class="page-header">
  <h1>My Lists</h1>
</div>
<form method="POST" action="/cinema/lists/create" class="create-list-form">
  <input type="text" name="name" placeholder="List name" required>
  <input type="text" name="description" placeholder="Description (optional)">
  <button type="submit">+ Create List</button>
</form>
<div class="lists-grid">
  <?php foreach ($lists as $l): ?>
  <div class="list-card">
    <a href="/cinema/lists/show/<?= $l['id'] ?>"><h3><?= htmlspecialchars($l['name']) ?></h3></a>
    <p><?= $l['count'] ?> films</p>
    <?php if ($l['description']): ?><p class="muted"><?= htmlspecialchars($l['description']) ?></p><?php endif; ?>
    <form method="POST" action="/cinema/lists/delete/<?= $l['id'] ?>">
      <button type="submit" class="btn-del">Delete</button>
    </form>
  </div>
  <?php endforeach; ?>
  <?php if (!$lists): ?><p class="muted">No lists yet.</p><?php endif; ?>
</div>
<?php require 'app/views/layout/footer.php'; ?>

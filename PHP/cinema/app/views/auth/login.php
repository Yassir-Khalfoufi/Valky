<?php require 'app/views/layout/header.php'; ?>
<div class="auth-box">
  <h1>Login</h1>
  <?php if (isset($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p>No account? <a href="/cinema/auth/register">Register</a></p>
</div>
<?php require 'app/views/layout/footer.php'; ?>

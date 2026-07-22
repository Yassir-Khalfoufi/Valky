<?php require 'app/views/layout/header.php'; ?>
<div class="auth-box">
  <h1>Register</h1>
  <?php if (isset($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
  </form>
  <p>Have an account? <a href="/cinema/auth/login">Login</a></p>
</div>
<?php require 'app/views/layout/footer.php'; ?>

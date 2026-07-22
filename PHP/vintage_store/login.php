<?php
// login.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) { header('Location: /index.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email)    $errors['email']    = 'Email is required.';
    if (!$password) $errors['password'] = 'Password is required.';

    if (empty($errors)) {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            flashMessage('success', 'Welcome back, ' . $user['username'] . '!');
            header('Location: /index.php'); exit;
        } else {
            $errors['general'] = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login — Le Grenier Vintage';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-box">
        <div class="auth-box__logo">
            <span class="logo__line">Le Grenier</span>
            <span class="logo__sub" style="display:block;text-align:center;letter-spacing:.18em;color:var(--sepia);font-size:.7rem;margin-top:.2rem">— Vintage —</span>
        </div>

        <h2>Welcome back</h2>
        <p class="auth-box__sub">Sign in to your account</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="flash flash--error" style="position:static;margin-bottom:1.2rem">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login.php">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       autocomplete="email" autofocus>
                <?php if (!empty($errors['email'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password">
                <?php if (!empty($errors['password'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn--primary btn--full">Sign in</button>
        </form>

        <p class="auth-box__switch">
            Don't have an account? <a href="/register.php">Register here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

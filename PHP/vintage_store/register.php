<?php
// register.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) { header('Location: /index.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    // Validation
    if (strlen($username) < 3) $errors['username'] = 'Username must be at least 3 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email.';
    if (strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors['confirm']  = 'Passwords do not match.';

    if (empty($errors)) {
        $pdo = getDB();

        // Check uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $errors['general'] = 'An account with this email or username already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hash]);

            $uid = $pdo->lastInsertId();
            $_SESSION['user_id']  = $uid;
            $_SESSION['username'] = $username;
            $_SESSION['role']     = 'user';

            flashMessage('success', 'Welcome to Le Grenier, ' . $username . '!');
            header('Location: /index.php'); exit;
        }
    }
}

$pageTitle = 'Register — Le Grenier Vintage';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-box">
        <div class="auth-box__logo">
            <span class="logo__line">Le Grenier</span>
            <span class="logo__sub" style="display:block;text-align:center;letter-spacing:.18em;color:var(--sepia);font-size:.7rem;margin-top:.2rem">— Vintage —</span>
        </div>

        <h2>Create Account</h2>
        <p class="auth-box__sub">Join our community of vintage lovers</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="flash flash--error" style="position:static;margin-bottom:1.2rem">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       autocomplete="username" autofocus minlength="3">
                <?php if (!empty($errors['username'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['username']) ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       autocomplete="email">
                <?php if (!empty($errors['email'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password <span id="pwStrength" style="font-size:.78rem;margin-left:.5rem"></span></label>
                <input type="password" id="password" name="password" autocomplete="new-password" minlength="6">
                <?php if (!empty($errors['password'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm password</label>
                <input type="password" id="confirm" name="confirm" autocomplete="new-password">
                <?php if (!empty($errors['confirm'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['confirm']) ?></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn--primary btn--full">Create account</button>
        </form>

        <p class="auth-box__switch">
            Already have an account? <a href="/login.php">Sign in</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

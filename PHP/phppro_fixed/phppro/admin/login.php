<?php
// admin/login.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in as admin
if (!empty($_SESSION['is_admin'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password =      $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $pdo  = getPDO();
            $stmt = $pdo->prepare(
                "SELECT id, username, password, is_admin FROM users WHERE username = ? LIMIT 1"
            );
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['is_admin']) {
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_admin'] = true;
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Access denied. Admin only.';
                }
            } else {
                $error = 'Incorrect username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again.';
        }
    }
}

$access_error = ($_GET['err'] ?? '') === 'access';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – The Vintage Style</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Montserrat', sans-serif;
      background: #f5f0ea;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      background: #fff;
      border-radius: 14px;
      padding: 48px 44px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 8px 40px rgba(81,53,31,.15);
    }
    .brand { text-align: center; margin-bottom: 32px; }
    .brand h1 { font-size: 1.5rem; color: #51351f; letter-spacing: 2px; text-transform: uppercase; }
    .brand p  { font-size: .8rem; color: #999; margin-top: 4px; }
    .badge-admin {
      display: inline-flex; align-items: center; gap: 6px;
      background: #51351f; color: #fff;
      font-size: .7rem; font-weight: 700; letter-spacing: 1px;
      padding: 4px 12px; border-radius: 20px; margin-bottom: 24px;
    }
    label { display: block; font-size: .8rem; font-weight: 600; color: #444; margin-bottom: 6px; }
    input[type=text], input[type=password] {
      width: 100%; padding: 12px 14px;
      border: 1.5px solid #e0d9d0; border-radius: 8px;
      font-size: .95rem; font-family: inherit;
      background: #fdf9f5;
      transition: border-color .2s;
      margin-bottom: 18px;
    }
    input:focus { outline: none; border-color: #51351f; }
    .btn {
      width: 100%; padding: 13px;
      background: #51351f; color: #fff;
      border: none; border-radius: 8px;
      font-size: 1rem; font-weight: 700; font-family: inherit;
      cursor: pointer; letter-spacing: 1px;
      transition: background .2s, box-shadow .2s;
    }
    .btn:hover { background: #3a2410; box-shadow: 0 6px 20px rgba(81,53,31,.3); }
    .alert {
      padding: 10px 14px; border-radius: 8px;
      font-size: .85rem; margin-bottom: 20px;
    }
    .alert-error   { background: #fde8e8; color: #c0392b; }
    .alert-warning { background: #fff3cd; color: #856404; }
    .back-link { text-align: center; margin-top: 22px; font-size: .8rem; }
    .back-link a { color: #51351f; font-weight: 600; text-decoration: none; }
    .back-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="card">
    <div class="brand">
      <h1>The Vintage Style</h1>
      <p>Administration Panel</p>
    </div>
    <div style="text-align:center">
      <span class="badge-admin">🔐 Admin Access</span>
    </div>

    <?php if ($access_error): ?>
      <div class="alert alert-warning">Access denied. Admin credentials required.</div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username">

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required autocomplete="current-password">

      <button class="btn" type="submit">Login to Admin Panel</button>
    </form>

    <div class="back-link">
      <a href="../home.html">← Back to Store</a>
    </div>
  </div>
</body>
</html>

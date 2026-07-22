<?php
// login.php
require_once 'auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php'); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $remember   = !empty($_POST['remember']);

    if ($identifier === '' || $password === '') {
        $error = 'Remplis tous les champs.';
    } else {
        $pdo  = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email OR username = :username LIMIT 1');
$stmt->execute([
    ':email'    => $identifier,
    ':username' => $identifier
]);
$user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user, $remember);
            header('Location: index.php'); exit;
        } else {
            $error = 'Identifiants incorrects.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CineList — Connexion</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body{display:flex;align-items:center;justify-content:center;min-height:100vh}
    .auth-card{width:100%;max-width:400px;background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:2.5rem 2rem;box-shadow:0 8px 40px rgba(0,0,0,.6)}
    .auth-logo{font-family:'DM Serif Display',serif;font-style:italic;font-size:2.4rem;background:linear-gradient(120deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-align:center;margin-bottom:.3rem}
    .auth-sub{text-align:center;color:var(--muted);font-size:.83rem;margin-bottom:2rem}
    .error-box{background:rgba(224,82,82,.12);border:1px solid rgba(224,82,82,.35);color:#f08080;padding:.7rem 1rem;border-radius:7px;font-size:.85rem;margin-bottom:1.2rem}
    .remember-row{display:flex;align-items:center;gap:.5rem;font-size:.83rem;color:var(--muted);margin-bottom:1.2rem}
    .remember-row input[type=checkbox]{accent-color:var(--accent);width:15px;height:15px;cursor:pointer}
    .auth-footer{text-align:center;margin-top:1.4rem;font-size:.82rem;color:var(--muted)}
    .auth-footer a{color:var(--accent);text-decoration:none}
    .auth-footer a:hover{text-decoration:underline}
  </style>
</head>
<body>
<div class="auth-card">
  <div class="auth-logo">CineList</div>
  <p class="auth-sub">Connexion à ta collection</p>

  <?php if ($error): ?>
    <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <div class="form-group">
      <label for="identifier">Email ou nom d'utilisateur</label>
      <input type="text" id="identifier" name="identifier"
             value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>"
             placeholder="ex: admin" autofocus required>
    </div>
    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required>
    </div>
    <div class="remember-row">
      <input type="checkbox" id="remember" name="remember" <?= !empty($_POST['remember']) ? 'checked' : '' ?>>
      <label for="remember">Se souvenir de moi (30 jours)</label>
    </div>
    <button type="submit" class="btn btn-primary">Se connecter</button>
  </form>
  <p class="auth-footer">Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
</div>
</body>
</html>

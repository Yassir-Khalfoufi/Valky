<?php
// register.php
require_once 'auth.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php'); exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validation
    if ($username === '' || $email === '' || $password === '') {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Le nom d\'utilisateur doit faire entre 3 et 50 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit faire au moins 6 caractères.';
    } elseif ($password !== $password2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $pdo = getPDO();

        // Vérifier unicité
        $check = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
        $check->execute([':u' => $username, ':e' => $email]);
        if ($check->fetch()) {
            $error = 'Ce nom d\'utilisateur ou email est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:u, :e, :p)')
                ->execute([':u' => $username, ':e' => $email, ':p' => $hash]);

            $success = 'Compte créé ! Tu peux maintenant te connecter.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CineList — Inscription</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .auth-card {
      width: 100%;
      max-width: 420px;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 2.5rem 2rem;
      box-shadow: 0 8px 40px rgba(0,0,0,.6);
    }
    .auth-logo {
      font-family: 'DM Serif Display', serif;
      font-style: italic;
      font-size: 2.4rem;
      background: linear-gradient(120deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-align: center;
      margin-bottom: .3rem;
    }
    .auth-sub { text-align: center; color: var(--muted); font-size: .83rem; margin-bottom: 2rem; }
    .error-box {
      background: rgba(224,82,82,.12);
      border: 1px solid rgba(224,82,82,.35);
      color: #f08080;
      padding: .7rem 1rem;
      border-radius: 7px;
      font-size: .85rem;
      margin-bottom: 1.2rem;
    }
    .success-box {
      background: rgba(82,201,122,.12);
      border: 1px solid rgba(82,201,122,.35);
      color: #6fdc96;
      padding: .7rem 1rem;
      border-radius: 7px;
      font-size: .85rem;
      margin-bottom: 1.2rem;
    }
    .auth-footer { text-align: center; margin-top: 1.4rem; font-size: .82rem; color: var(--muted); }
    .auth-footer a { color: var(--accent); text-decoration: none; }
    .auth-footer a:hover { text-decoration: underline; }
    .hint { font-size: .73rem; color: var(--muted); margin-top: .25rem; }
  </style>
</head>
<body>
<div class="auth-card">
  <div class="auth-logo">CineList</div>
  <p class="auth-sub">Créer un compte</p>

  <?php if ($error): ?>
    <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="success-box">✓ <?= htmlspecialchars($success) ?> <a href="login.php" style="color:var(--accent)">→ Connexion</a></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label for="username">Nom d'utilisateur</label>
      <input type="text" id="username" name="username"
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
             placeholder="ex: valky" minlength="3" maxlength="50" autofocus required>
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
             placeholder="ex: moi@email.com" required>
    </div>

    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" placeholder="Min. 6 caractères" minlength="6" required>
    </div>

    <div class="form-group">
      <label for="password2">Confirmer le mot de passe</label>
      <input type="password" id="password2" name="password2" placeholder="Répète le mot de passe" required>
    </div>

    <button type="submit" class="btn btn-primary">Créer mon compte</button>
  </form>

  <p class="auth-footer">Déjà un compte ? <a href="login.php">Se connecter</a></p>
</div>
</body>
</html>

<?php
// index.php — Connexion
require_once 'includes/auth.php';
require_once 'includes/db.php';

startSession();

// Déjà connecté → rediriger
if (isLoggedIn()) {
    header('Location: pages/etudiants.php');
    exit;
}

$error   = '';
$success = '';

// ----- Connexion -----
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $pdo  = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nom']  = $user['nom'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: pages/etudiants.php');
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}

// ----- Inscription -----
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $nom   = trim($_POST['nom']   ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']   ?? '';
    $role  = in_array($_POST['role'] ?? '', ['admin','prof']) ? $_POST['role'] : 'prof';

    if ($nom === '' || $email === '' || $pass === '') {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif (strlen($pass) < 6) {
        $error = 'Le mot de passe doit comporter au moins 6 caractères.';
    } else {
        $pdo  = getPDO();
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $ins  = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)');
            $ins->execute([$nom, $email, $hash, $role]);
            $success = 'Compte créé avec succès. Vous pouvez vous connecter.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniGestion — Connexion</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tabs { display: flex; gap: 0; margin-bottom: 24px; border-bottom: 2px solid #e5e7eb; }
        .tab-btn {
            flex: 1; padding: 10px; border: none; background: none;
            cursor: pointer; font-size: 14px; font-weight: bold; color: #6b7280;
        }
        .tab-btn.active { color: #2563eb; border-bottom: 2px solid #2563eb; margin-bottom: -2px; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <h1>🎓 UniGestion</h1>

        <?php if ($error):   ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('login')">Connexion</button>
            <button class="tab-btn"        onclick="switchTab('register')">Inscription</button>
        </div>

        <!-- Connexion -->
        <div class="tab-panel active" id="tab-login">
            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" placeholder="admin@univ.ma" required>
                </div>
                <div class="form-group">
                    <label for="login-pass">Mot de passe</label>
                    <input type="password" id="login-pass" name="password" placeholder="••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Se connecter</button>
            </form>
        </div>

        <!-- Inscription -->
        <div class="tab-panel" id="tab-register">
            <form method="POST" action="">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="nom" placeholder="Jean Dupont" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="jean@univ.ma" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="6 caractères minimum" required>
                </div>
                <div class="form-group">
                    <label>Rôle</label>
                    <select name="role">
                        <option value="prof">Professeur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success" style="width:100%">Créer un compte</button>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach((b, i) => {
        b.classList.toggle('active', (i === 0 ? 'login' : 'register') === tab);
    });
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
}
</script>
</body>
</html>

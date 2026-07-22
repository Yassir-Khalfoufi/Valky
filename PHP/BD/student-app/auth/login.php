<?php
// auth/login.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

demarrer_session();

// déjà connecté → rediriger
if (est_connecte()) {
    header('Location: /etudiants/liste.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    // validation basique
    if ($email === '') {
        $errors['email'] = 'L\'adresse e-mail est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    }
    if ($mdp === '') {
        $errors['mot_de_passe'] = 'Le mot de passe est obligatoire.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('select id, nom, email, mot_de_passe, role from utilisateurs where email = ?');
            $stmt->execute([$email]);
            $utilisateur = $stmt->fetch();

            if ($utilisateur && password_verify($mdp, $utilisateur['mot_de_passe'])) {
                // régénérer l'ID de session (sécurité)
                session_regenerate_id(true);
                $_SESSION['user_id']    = $utilisateur['id'];
                $_SESSION['user_nom']   = $utilisateur['nom'];
                $_SESSION['user_role']  = $utilisateur['role'];
                $_SESSION['user_email'] = $utilisateur['email'];

                set_flash('success', 'Bienvenue, ' . $utilisateur['nom'] . ' !');
                header('Location: /etudiants/liste.php');
                exit;
            } else {
                $errors['global'] = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $errors['global'] = 'Erreur serveur. Veuillez réessayer.';
        }
    }
}

$titre = 'Connexion';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-box">
    <div class="card">
        <h1>Connexion</h1>

        <?php if (!empty($errors['global'])): ?>
            <div class="flash flash-error"><?= htmlspecialchars($errors['global']) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       autocomplete="email" required>
                <?php if (!empty($errors['email'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       autocomplete="current-password" required>
                <?php if (!empty($errors['mot_de_passe'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['mot_de_passe']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>

        <p class="text-muted mt-2">
            Pas encore de compte ? <a href="/auth/inscription.php">S'inscrire</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

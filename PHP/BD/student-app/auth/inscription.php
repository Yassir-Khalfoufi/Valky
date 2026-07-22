<?php
// auth/inscription.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

demarrer_session();

if (est_connecte()) {
    header('Location: /etudiants/liste.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';
    $mdp2  = $_POST['confirmation'] ?? '';
    $role  = $_POST['role'] ?? 'prof';

    // validation
    if ($nom === '') {
        $errors['nom'] = 'Le nom est obligatoire.';
    }
    if ($email === '') {
        $errors['email'] = 'L\'e-mail est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail invalide.';
    }
    if (strlen($mdp) < 6) {
        $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    if ($mdp !== $mdp2) {
        $errors['confirmation'] = 'Les mots de passe ne correspondent pas.';
    }
    if (!in_array($role, ['admin', 'prof'])) {
        $role = 'prof';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('select id from utilisateurs where email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Cet e-mail est déjà utilisé.';
            } else {
                $hash = password_hash($mdp, PASSWORD_BCRYPT);
                $ins  = $pdo->prepare('insert into utilisateurs (nom, email, mot_de_passe, role) values (?, ?, ?, ?)');
                $ins->execute([$nom, $email, $hash, $role]);

                set_flash('success', 'Compte créé avec succès. Connectez-vous.');
                header('Location: /auth/login.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors['global'] = 'Erreur serveur. Veuillez réessayer.';
        }
    }
}

$titre = 'Inscription';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-box">
    <div class="card">
        <h1>Créer un compte</h1>

        <?php if (!empty($errors['global'])): ?>
            <div class="flash flash-error"><?= htmlspecialchars($errors['global']) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                <?php if (!empty($errors['nom'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['nom']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role">Rôle</label>
                <select id="role" name="role">
                    <option value="prof"  <?= (($_POST['role'] ?? '') === 'prof'  ? 'selected' : '') ?>>Professeur</option>
                    <option value="admin" <?= (($_POST['role'] ?? '') === 'admin' ? 'selected' : '') ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe <span class="text-muted">(min. 6 caractères)</span></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                <?php if (!empty($errors['mot_de_passe'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['mot_de_passe']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirmation">Confirmer le mot de passe</label>
                <input type="password" id="confirmation" name="confirmation" required>
                <?php if (!empty($errors['confirmation'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Créer le compte</button>
        </form>

        <p class="text-muted mt-2">
            Déjà inscrit ? <a href="/auth/login.php">Se connecter</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

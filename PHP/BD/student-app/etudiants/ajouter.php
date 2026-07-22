<?php
// etudiants/ajouter.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

exiger_connexion();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom            = trim($_POST['nom']            ?? '');
    $prenom         = trim($_POST['prenom']         ?? '');
    $email          = trim($_POST['email']          ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $filiere        = trim($_POST['filiere']        ?? '');

    // validation
    if ($nom === '')            { $errors['nom']            = 'Le nom est obligatoire.'; }
    if ($prenom === '')         { $errors['prenom']         = 'Le prénom est obligatoire.'; }
    if ($email === '')          { $errors['email']          = 'L\'e-mail est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                  $errors['email']          = 'E-mail invalide.'; }
    if ($date_naissance === '') { $errors['date_naissance'] = 'La date de naissance est obligatoire.'; }
    if ($filiere === '')        { $errors['filiere']        = 'La filière est obligatoire.'; }

    if (empty($errors)) {
        try {
            // vérifier unicité e-mail
            $check = $pdo->prepare('select id from etudiants where email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors['email'] = 'Cet e-mail est déjà utilisé par un autre étudiant.';
            } else {
                $ins = $pdo->prepare(
                    'insert into etudiants (nom, prenom, email, date_naissance, filiere) values (?, ?, ?, ?, ?)'
                );
                $ins->execute([$nom, $prenom, $email, $date_naissance, $filiere]);

                set_flash('success', 'Étudiant ajouté avec succès.');
                header('Location: /etudiants/liste.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors['global'] = 'Erreur serveur. Veuillez réessayer.';
        }
    }
}

$titre = 'Ajouter un étudiant';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card" style="max-width:600px">
    <h1>Ajouter un étudiant</h1>

    <?php if (!empty($errors['global'])): ?>
        <div class="flash flash-error"><?= htmlspecialchars($errors['global']) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
            <?php if (!empty($errors['nom'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['nom']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom"
                   value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
            <?php if (!empty($errors['prenom'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['prenom']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance"
                   value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>" required>
            <?php if (!empty($errors['date_naissance'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['date_naissance']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="filiere">Filière</label>
            <input type="text" id="filiere" name="filiere"
                   value="<?= htmlspecialchars($_POST['filiere'] ?? '') ?>"
                   placeholder="ex : Informatique, Génie civil…" required>
            <?php if (!empty($errors['filiere'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['filiere']) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex-between">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/etudiants/liste.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

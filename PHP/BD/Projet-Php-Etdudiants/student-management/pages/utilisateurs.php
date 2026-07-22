<?php
// pages/utilisateurs.php — Gestion des utilisateurs (admin uniquement)
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireLogin();

// Réservé aux admins
if (!isAdmin()) {
    header('Location: etudiants.php');
    exit;
}

$pdo     = getPDO();
$message = '';
$msgType = 'success';

// ---- Suppression
if (isset($_GET['action']) && $_GET['action'] === 'delete' && (int)($_GET['id'] ?? 0) > 0) {
    verifyCsrf();
    $delId = (int)$_GET['id'];
    // Empêcher de supprimer son propre compte
    if ($delId === (int)currentUser()['id']) {
        $message = 'Vous ne pouvez pas supprimer votre propre compte.';
        $msgType = 'error';
    } else {
        $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?')->execute([$delId]);
        $message = 'Utilisateur supprimé.';
    }
}

// ---- Modification du rôle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    verifyCsrf();
    $uid  = (int)$_POST['user_id'];
    $role = in_array($_POST['role'], ['admin','prof']) ? $_POST['role'] : 'prof';
    $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?')->execute([$role, $uid]);
    $message = 'Rôle mis à jour.';
}

// ---- Ajout d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    verifyCsrf();
    $nom   = trim($_POST['nom']   ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']   ?? '';
    $role  = in_array($_POST['role'] ?? '', ['admin','prof']) ? $_POST['role'] : 'prof';

    $errors = [];
    if ($nom === '')  $errors[] = 'Nom obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if (strlen($pass) < 6) $errors[] = 'Mot de passe trop court (6 car. min).';

    if (empty($errors)) {
        $chk = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $message = 'Cet email est déjà utilisé.';
            $msgType = 'error';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?,?,?,?)')
                ->execute([$nom, $email, $hash, $role]);
            $message = 'Utilisateur ajouté.';
        }
    } else {
        $message = implode('<br>', $errors);
        $msgType = 'error';
    }
}

$users = $pdo->query('SELECT id, nom, email, role, created_at FROM utilisateurs ORDER BY id')->fetchAll();

include '../includes/header.php';
?>

<h1>Gestion des Utilisateurs</h1>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>"><?= $message ?></div>
<?php endif; ?>

<!-- Formulaire ajout -->
<div class="card">
    <h2>Ajouter un utilisateur</h2>
    <form method="POST" action="utilisateurs.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="add_user"   value="1">
        <div class="form-row">
            <div class="form-group">
                <label>Nom complet *</label>
                <input type="text" name="nom" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Mot de passe * (6 car. min)</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="prof">Professeur</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success">➕ Ajouter</button>
    </form>
</div>

<!-- Tableau des utilisateurs -->
<div class="card">
    <h2>Liste des utilisateurs (<?= count($users) ?>)</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Inscrit le</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= e($u['nom']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td>
                    <form method="POST" action="utilisateurs.php" style="display:inline-flex;gap:6px;align-items:center">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="update_role" value="1">
                        <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                        <select name="role" style="padding:4px 8px;font-size:13px;border:1px solid #ccc;border-radius:4px">
                            <option value="prof"  <?= $u['role'] === 'prof'  ? 'selected' : '' ?>>prof</option>
                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                        </select>
                        <button type="submit" class="btn btn-warning" style="padding:4px 10px;font-size:12px">✔</button>
                    </form>
                </td>
                <td><?= e(substr($u['created_at'], 0, 10)) ?></td>
                <td>
                    <?php if ((int)$u['id'] !== (int)currentUser()['id']): ?>
                    <form method="POST" action="utilisateurs.php?action=delete&id=<?= (int)$u['id'] ?>"
                          onsubmit="return confirm('Supprimer cet utilisateur ?')" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <button type="submit" class="btn btn-danger">🗑️</button>
                    </form>
                    <?php else: ?>
                    <span style="color:#9ca3af;font-size:13px">(vous)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<?php
// pages/etudiants.php — Liste, ajout, modification, suppression des étudiants
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireLogin();

$pdo     = getPDO();
$message = '';
$msgType = 'success';

$filieres = ['Informatique','Mathematiques','Physique','Chimie','Biologie','Economie','Droit','Lettres','Autre'];

// ------------------------------------------------------------------ ACTIONS
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

// Suppression
if ($action === 'delete' && $id > 0) {
    verifyCsrf();
    $pdo->prepare('DELETE FROM etudiants WHERE id = ?')->execute([$id]);
    $message = 'Étudiant supprimé avec succès.';
    $action  = 'list';
}

// Enregistrement (ajout / modification)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    verifyCsrf();

    $nom      = trim($_POST['nom']      ?? '');
    $prenom   = trim($_POST['prenom']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $dob      = $_POST['date_naissance'] ?? '';
    $filiere  = trim($_POST['filiere']  ?? '');
    $editId   = (int)($_POST['edit_id'] ?? 0);

    // Validation
    $errors = [];
    if ($nom    === '') $errors[] = 'Le nom est obligatoire.';
    if ($prenom === '') $errors[] = 'Le prénom est obligatoire.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if ($dob    === '' || !strtotime($dob)) $errors[] = 'Date de naissance invalide.';
    if ($filiere === '') $errors[] = 'La filière est obligatoire.';

    if (empty($errors)) {
        if ($editId > 0) {
            // Mise à jour
            $stmt = $pdo->prepare(
                'UPDATE etudiants SET nom=?, prenom=?, email=?, date_naissance=?, filiere=? WHERE id=?'
            );
            $stmt->execute([$nom, $prenom, $email, $dob, $filiere, $editId]);
            $message = 'Étudiant modifié avec succès.';
        } else {
            // Insertion — vérifier doublon email
            $chk = $pdo->prepare('SELECT id FROM etudiants WHERE email = ?');
            $chk->execute([$email]);
            if ($chk->fetch()) {
                $message = 'Cet email est déjà utilisé.';
                $msgType = 'error';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO etudiants (nom, prenom, email, date_naissance, filiere) VALUES (?,?,?,?,?)'
                );
                $stmt->execute([$nom, $prenom, $email, $dob, $filiere]);
                $message = 'Étudiant ajouté avec succès.';
            }
        }
        $action = 'list';
    } else {
        $message = implode('<br>', $errors);
        $msgType = 'error';
        $action  = ($editId > 0) ? 'edit' : 'add';
    }
}

// Charger l'étudiant à modifier
$etudiant = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM etudiants WHERE id = ?');
    $stmt->execute([$id]);
    $etudiant = $stmt->fetch();
    if (!$etudiant) { $action = 'list'; }
}

// ------------------------------------------------------------------ LIST DATA
$search  = trim($_GET['search']  ?? '');
$fFilter = trim($_GET['filiere'] ?? '');

$sql    = 'SELECT * FROM etudiants WHERE 1=1';
$params = [];

if ($search !== '') {
    $sql .= ' AND (nom LIKE ? OR prenom LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($fFilter !== '') {
    $sql .= ' AND filiere = ?';
    $params[] = $fFilter;
}
$sql .= ' ORDER BY nom, prenom';

$stmt      = $pdo->prepare($sql);
$stmt->execute($params);
$etudiants = $stmt->fetchAll();

// Stats
$total       = (int)$pdo->query('SELECT COUNT(*) FROM etudiants')->fetchColumn();
$totalFil    = (int)$pdo->query('SELECT COUNT(DISTINCT filiere) FROM etudiants')->fetchColumn();

include '../includes/header.php';
?>

<h1>Gestion des Étudiants</h1>

<!-- Stats -->
<div class="stats">
    <div class="stat-box">
        <div class="stat-num"><?= $total ?></div>
        <div class="stat-label">Étudiants inscrits</div>
    </div>
    <div class="stat-box">
        <div class="stat-num"><?= $totalFil ?></div>
        <div class="stat-label">Filières actives</div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>"><?= $message ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<!-- =================== FORMULAIRE =================== -->
<div class="card">
    <h2><?= $action === 'edit' ? 'Modifier un étudiant' : 'Ajouter un étudiant' ?></h2>
    <form method="POST" action="etudiants.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="save"    value="1">
        <input type="hidden" name="edit_id" value="<?= $etudiant['id'] ?? 0 ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Nom *</label>
                <input type="text" name="nom" value="<?= e($etudiant['nom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Prénom *</label>
                <input type="text" name="prenom" value="<?= e($etudiant['prenom'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= e($etudiant['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Date de naissance *</label>
                <input type="date" name="date_naissance" value="<?= e($etudiant['date_naissance'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Filière *</label>
            <select name="filiere" required>
                <option value="">— Choisir —</option>
                <?php foreach ($filieres as $f): ?>
                    <option value="<?= e($f) ?>" <?= ($etudiant['filiere'] ?? '') === $f ? 'selected' : '' ?>>
                        <?= e($f) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex;gap:10px">
            <button type="submit" class="btn btn-success">
                <?= $action === 'edit' ? '💾 Enregistrer' : '➕ Ajouter' ?>
            </button>
            <a href="etudiants.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php else: ?>
<!-- =================== LISTE =================== -->
<div class="card">
    <!-- Barre de recherche -->
    <form method="GET" action="etudiants.php">
        <div class="toolbar">
            <input type="text"   name="search"  value="<?= e($search) ?>"  placeholder="Rechercher par nom / prénom…">
            <select name="filiere">
                <option value="">Toutes les filières</option>
                <?php foreach ($filieres as $f): ?>
                    <option value="<?= e($f) ?>" <?= $fFilter === $f ? 'selected' : '' ?>><?= e($f) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
            <a href="etudiants.php" class="btn btn-secondary">✖ Réinitialiser</a>
            <a href="etudiants.php?action=add" class="btn btn-success" style="margin-left:auto">➕ Ajouter un étudiant</a>
        </div>
    </form>

    <!-- Tableau -->
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date naissance</th>
                    <th>Filière</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($etudiants)): ?>
                <tr><td colspan="7" style="text-align:center;color:#888">Aucun étudiant trouvé.</td></tr>
            <?php else: ?>
                <?php foreach ($etudiants as $et): ?>
                <tr>
                    <td><?= (int)$et['id'] ?></td>
                    <td><?= e($et['nom']) ?></td>
                    <td><?= e($et['prenom']) ?></td>
                    <td><?= e($et['email']) ?></td>
                    <td><?= e($et['date_naissance']) ?></td>
                    <td><span class="badge"><?= e($et['filiere']) ?></span></td>
                    <td>
                        <div class="actions">
                            <a href="notes.php?etudiant_id=<?= (int)$et['id'] ?>" class="btn btn-primary" title="Voir notes">📋</a>
                            <a href="etudiants.php?action=edit&id=<?= (int)$et['id'] ?>" class="btn btn-warning">✏️</a>
                            <form method="POST" action="etudiants.php" style="display:inline"
                                  onsubmit="return confirm('Supprimer cet étudiant ?')">
                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="_action_delete" value="1">
                                <button type="submit" class="btn btn-danger"
                                        formaction="etudiants.php?action=delete&id=<?= (int)$et['id'] ?>">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>

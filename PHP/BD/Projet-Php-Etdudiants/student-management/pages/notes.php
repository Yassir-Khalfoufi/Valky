<?php
// pages/notes.php — Gestion des notes
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireLogin();

$pdo     = getPDO();
$message = '';
$msgType = 'success';

$etudiantId = (int)($_GET['etudiant_id'] ?? 0);

// Charger la liste des étudiants pour le sélecteur
$etudiants = $pdo->query('SELECT id, nom, prenom FROM etudiants ORDER BY nom, prenom')->fetchAll();

// Etudiant courant
$etudiant = null;
if ($etudiantId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM etudiants WHERE id = ?');
    $stmt->execute([$etudiantId]);
    $etudiant = $stmt->fetch();
}

// ---- Suppression d'une note
if (isset($_GET['delete_note']) && (int)$_GET['delete_note'] > 0) {
    verifyCsrf();
    $pdo->prepare('DELETE FROM notes WHERE id = ? AND etudiant_id = ?')
        ->execute([(int)$_GET['delete_note'], $etudiantId]);
    $message = 'Note supprimée.';
}

// ---- Enregistrement d'une note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    verifyCsrf();

    $matiere   = trim($_POST['matiere']    ?? '');
    $note      = $_POST['note']            ?? '';
    $noteId    = (int)($_POST['note_id']   ?? 0);
    $eId       = (int)($_POST['etudiant_id'] ?? 0);
    $etudiantId = $eId;

    $errors = [];
    if ($matiere === '') $errors[] = 'La matière est obligatoire.';
    if ($note === '' || !is_numeric($note) || (float)$note < 0 || (float)$note > 20)
        $errors[] = 'La note doit être un nombre entre 0 et 20.';
    if ($eId <= 0) $errors[] = 'Étudiant invalide.';

    if (empty($errors)) {
        if ($noteId > 0) {
            $pdo->prepare('UPDATE notes SET matiere=?, note=? WHERE id=? AND etudiant_id=?')
                ->execute([$matiere, (float)$note, $noteId, $eId]);
            $message = 'Note modifiée.';
        } else {
            $pdo->prepare('INSERT INTO notes (etudiant_id, matiere, note) VALUES (?,?,?)')
                ->execute([$eId, $matiere, (float)$note]);
            $message = 'Note ajoutée.';
        }
        // Reload etudiant
        $stmt = $pdo->prepare('SELECT * FROM etudiants WHERE id = ?');
        $stmt->execute([$eId]);
        $etudiant = $stmt->fetch();
    } else {
        $message = implode('<br>', $errors);
        $msgType = 'error';
    }
}

// ---- Charger les notes de l'étudiant sélectionné
$notes   = [];
$moyenne = null;
$editNote = null;

if ($etudiant) {
    $stmt = $pdo->prepare('SELECT * FROM notes WHERE etudiant_id = ? ORDER BY matiere');
    $stmt->execute([$etudiant['id']]);
    $notes = $stmt->fetchAll();

    if (!empty($notes)) {
        $moyenne = array_sum(array_column($notes, 'note')) / count($notes);
    }

    // Note à éditer ?
    if (isset($_GET['edit_note']) && (int)$_GET['edit_note'] > 0) {
        foreach ($notes as $n) {
            if ((int)$n['id'] === (int)$_GET['edit_note']) {
                $editNote = $n;
                break;
            }
        }
    }
}

include '../includes/header.php';
?>

<h1>Gestion des Notes</h1>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType === 'error' ? 'error' : 'success' ?>"><?= $message ?></div>
<?php endif; ?>

<!-- Sélecteur d'étudiant -->
<div class="card">
    <form method="GET" action="notes.php" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <label style="font-weight:bold;white-space:nowrap">Étudiant :</label>
        <select name="etudiant_id" style="padding:7px 12px;border:1px solid #ccc;border-radius:4px;font-size:14px;min-width:200px">
            <option value="">— Sélectionner —</option>
            <?php foreach ($etudiants as $et): ?>
                <option value="<?= (int)$et['id'] ?>" <?= $etudiant && $etudiant['id'] == $et['id'] ? 'selected' : '' ?>>
                    <?= e($et['nom'] . ' ' . $et['prenom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Afficher les notes</button>
    </form>
</div>

<?php if ($etudiant): ?>

<!-- Infos étudiant -->
<div class="card" style="background:#f0f7ff;border-color:#bfdbfe">
    <strong><?= e($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></strong>
    — <?= e($etudiant['filiere']) ?>
    — <?= e($etudiant['email']) ?>
    <?php if ($moyenne !== null): ?>
        &nbsp;&nbsp;
        <span style="font-size:15px;font-weight:bold;color:<?= $moyenne >= 10 ? '#065f46' : '#991b1b' ?>">
            Moyenne : <?= number_format($moyenne, 2) ?> / 20
        </span>
    <?php endif; ?>
</div>

<!-- Formulaire d'ajout / modification -->
<div class="card">
    <h2><?= $editNote ? 'Modifier la note' : 'Ajouter une note' ?></h2>
    <form method="POST" action="notes.php?etudiant_id=<?= (int)$etudiant['id'] ?>">
        <input type="hidden" name="csrf_token"   value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="save_note"    value="1">
        <input type="hidden" name="etudiant_id"  value="<?= (int)$etudiant['id'] ?>">
        <input type="hidden" name="note_id"      value="<?= (int)($editNote['id'] ?? 0) ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Matière *</label>
                <input type="text" name="matiere" value="<?= e($editNote['matiere'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Note (0–20) *</label>
                <input type="number" name="note" min="0" max="20" step="0.25"
                       value="<?= e($editNote['note'] ?? '') ?>" required>
            </div>
        </div>

        <div style="display:flex;gap:10px">
            <button type="submit" class="btn btn-success">
                <?= $editNote ? '💾 Modifier' : '➕ Ajouter' ?>
            </button>
            <?php if ($editNote): ?>
                <a href="notes.php?etudiant_id=<?= (int)$etudiant['id'] ?>" class="btn btn-secondary">Annuler</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tableau des notes -->
<div class="card">
    <h2>Notes de <?= e($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h2>
    <?php if (empty($notes)): ?>
        <p style="color:#888">Aucune note enregistrée.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Note / 20</th>
                    <th>Appréciation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($notes as $n):
                $val = (float)$n['note'];
                $appr = match(true) {
                    $val >= 16 => '✅ Très bien',
                    $val >= 14 => '👍 Bien',
                    $val >= 12 => '🆗 Assez bien',
                    $val >= 10 => '🟡 Passable',
                    default    => '❌ Insuffisant',
                };
            ?>
            <tr>
                <td><?= e($n['matiere']) ?></td>
                <td><strong><?= number_format($val, 2) ?></strong></td>
                <td><?= $appr ?></td>
                <td>
                    <div class="actions">
                        <a href="notes.php?etudiant_id=<?= (int)$etudiant['id'] ?>&edit_note=<?= (int)$n['id'] ?>" class="btn btn-warning">✏️</a>
                        <form method="POST"
                              action="notes.php?etudiant_id=<?= (int)$etudiant['id'] ?>&delete_note=<?= (int)$n['id'] ?>"
                              onsubmit="return confirm('Supprimer cette note ?')" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <button type="submit" class="btn btn-danger">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Moyenne générale</strong></td>
                    <td colspan="3">
                        <strong style="color:<?= $moyenne >= 10 ? '#065f46' : '#991b1b' ?>">
                            <?= number_format($moyenne, 2) ?> / 20
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>

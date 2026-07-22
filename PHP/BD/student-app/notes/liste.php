<?php
// notes/liste.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

exiger_connexion();

$etudiant_id = (int)($_GET['etudiant_id'] ?? 0);
if ($etudiant_id <= 0) {
    set_flash('error', 'Identifiant étudiant invalide.');
    header('Location: /etudiants/liste.php');
    exit;
}

// charger l'étudiant
try {
    $stmt = $pdo->prepare('select * from etudiants where id = ?');
    $stmt->execute([$etudiant_id]);
    $etudiant = $stmt->fetch();
} catch (PDOException $e) {
    $etudiant = null;
}

if (!$etudiant) {
    set_flash('error', 'Étudiant introuvable.');
    header('Location: /etudiants/liste.php');
    exit;
}

$errors = [];

// traitement du formulaire d'ajout / modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action']  ?? '';
    $matiere = trim($_POST['matiere'] ?? '');
    $note_val = $_POST['note']   ?? '';
    $note_id  = (int)($_POST['note_id'] ?? 0);

    if ($matiere === '') {
        $errors['matiere'] = 'La matière est obligatoire.';
    }
    if ($note_val === '' || !is_numeric($note_val)) {
        $errors['note'] = 'La note doit être un nombre.';
    } elseif ((float)$note_val < 0 || (float)$note_val > 20) {
        $errors['note'] = 'La note doit être comprise entre 0 et 20.';
    }

    if (empty($errors)) {
        $note_val = round((float)$note_val, 2);
        try {
            if ($action === 'modifier' && $note_id > 0) {
                $upd = $pdo->prepare('update notes set matiere = ?, note = ? where id = ? and etudiant_id = ?');
                $upd->execute([$matiere, $note_val, $note_id, $etudiant_id]);
                set_flash('success', 'Note modifiée.');
            } else {
                $ins = $pdo->prepare('insert into notes (etudiant_id, matiere, note) values (?, ?, ?)');
                $ins->execute([$etudiant_id, $matiere, $note_val]);
                set_flash('success', 'Note ajoutée.');
            }
            header('Location: /notes/liste.php?etudiant_id=' . $etudiant_id);
            exit;
        } catch (PDOException $e) {
            $errors['global'] = 'Erreur serveur. Veuillez réessayer.';
        }
    }
}

// supprimer une note
if (isset($_GET['supprimer'])) {
    $nid = (int)$_GET['supprimer'];
    try {
        $del = $pdo->prepare('delete from notes where id = ? and etudiant_id = ?');
        $del->execute([$nid, $etudiant_id]);
        set_flash('success', 'Note supprimée.');
    } catch (PDOException $e) {
        set_flash('error', 'Erreur lors de la suppression.');
    }
    header('Location: /notes/liste.php?etudiant_id=' . $etudiant_id);
    exit;
}

// charger note à modifier
$note_a_modifier = null;
if (isset($_GET['modifier'])) {
    $nid = (int)$_GET['modifier'];
    try {
        $ms = $pdo->prepare('select * from notes where id = ? and etudiant_id = ?');
        $ms->execute([$nid, $etudiant_id]);
        $note_a_modifier = $ms->fetch();
    } catch (PDOException $e) { /* ignore */ }
}

// charger toutes les notes + calculer moyenne
try {
    $nstmt = $pdo->prepare('select * from notes where etudiant_id = ? order by matiere');
    $nstmt->execute([$etudiant_id]);
    $notes = $nstmt->fetchAll();
} catch (PDOException $e) {
    $notes = [];
}

$moyenne = null;
if (!empty($notes)) {
    $somme   = array_sum(array_column($notes, 'note'));
    $moyenne = round($somme / count($notes), 2);
}

$titre = 'Notes de ' . $etudiant['prenom'] . ' ' . $etudiant['nom'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex-between mb-2">
    <h1>Notes &mdash; <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h1>
    <a href="/etudiants/liste.php" class="btn btn-secondary">&larr; Retour</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

    <!-- Formulaire ajout / modification -->
    <div class="card">
        <h2><?= $note_a_modifier ? 'Modifier la note' : 'Ajouter une note' ?></h2>

        <?php if (!empty($errors['global'])): ?>
            <div class="flash flash-error"><?= htmlspecialchars($errors['global']) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="action" value="<?= $note_a_modifier ? 'modifier' : 'ajouter' ?>">
            <?php if ($note_a_modifier): ?>
                <input type="hidden" name="note_id" value="<?= $note_a_modifier['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="matiere">Matière</label>
                <input type="text" id="matiere" name="matiere"
                       value="<?= htmlspecialchars($note_a_modifier['matiere'] ?? ($_POST['matiere'] ?? '')) ?>"
                       placeholder="ex : Mathématiques" required>
                <?php if (!empty($errors['matiere'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['matiere']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="note">Note <span class="text-muted">(0 – 20)</span></label>
                <input type="number" id="note" name="note" min="0" max="20" step="0.25"
                       value="<?= htmlspecialchars((string)($note_a_modifier['note'] ?? ($_POST['note'] ?? ''))) ?>"
                       required>
                <?php if (!empty($errors['note'])): ?>
                    <p class="form-error"><?= htmlspecialchars($errors['note']) ?></p>
                <?php endif; ?>
            </div>

            <div class="flex-between">
                <button type="submit" class="btn btn-primary">
                    <?= $note_a_modifier ? 'Enregistrer' : 'Ajouter' ?>
                </button>
                <?php if ($note_a_modifier): ?>
                    <a href="/notes/liste.php?etudiant_id=<?= $etudiant_id ?>" class="btn btn-secondary">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tableau des notes -->
    <div class="card">
        <h2>Relevé de notes</h2>
        <?php if (empty($notes)): ?>
            <p class="text-muted">Aucune note enregistrée.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Note / 20</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notes as $n): ?>
                    <tr>
                        <td><?= htmlspecialchars($n['matiere']) ?></td>
                        <td><?= number_format($n['note'], 2) ?></td>
                        <td>
                            <div class="actions">
                                <a href="?etudiant_id=<?= $etudiant_id ?>&modifier=<?= $n['id'] ?>"
                                   class="btn btn-secondary btn-sm">Modifier</a>
                                <a href="?etudiant_id=<?= $etudiant_id ?>&supprimer=<?= $n['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Supprimer cette note ?')">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="average-row">
                        <td>Moyenne générale</td>
                        <td><?= number_format($moyenne, 2) ?> / 20</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

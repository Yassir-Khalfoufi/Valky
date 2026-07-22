<?php
// etudiants/liste.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

exiger_connexion();

// paramètres de recherche / filtre
$recherche = trim($_GET['recherche'] ?? '');
$filiere   = trim($_GET['filiere']   ?? '');

// récupérer les filières disponibles pour le menu déroulant
try {
    $filieres_stmt = $pdo->query('select distinct filiere from etudiants order by filiere');
    $filieres = $filieres_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $filieres = [];
}

// construction de la requête dynamique
$sql    = 'select * from etudiants where 1=1';
$params = [];

if ($recherche !== '') {
    $sql    .= ' and (nom like ? or prenom like ?)';
    $params[] = '%' . $recherche . '%';
    $params[] = '%' . $recherche . '%';
}
if ($filiere !== '') {
    $sql    .= ' and filiere = ?';
    $params[] = $filiere;
}

$sql .= ' order by nom, prenom';

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $etudiants = $stmt->fetchAll();
} catch (PDOException $e) {
    $etudiants = [];
    set_flash('error', 'Impossible de charger la liste des étudiants.');
}

$titre = 'Liste des étudiants';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex-between mb-2">
    <h1>Étudiants</h1>
    <a href="/etudiants/ajouter.php" class="btn btn-primary">+ Ajouter un étudiant</a>
</div>

<!-- Barre de recherche / filtre (formulaire GET, pas de JS) -->
<form method="get" action="" class="card">
    <div class="filter-bar">
        <div class="form-group">
            <label for="recherche">Recherche (nom / prénom)</label>
            <input type="text" id="recherche" name="recherche"
                   value="<?= htmlspecialchars($recherche) ?>"
                   placeholder="ex : Lamine">
        </div>

        <div class="form-group">
            <label for="filiere">Filière</label>
            <select id="filiere" name="filiere">
                <option value="">— Toutes —</option>
                <?php foreach ($filieres as $f): ?>
                    <option value="<?= htmlspecialchars($f) ?>"
                        <?= $filiere === $f ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-secondary">Filtrer</button>
        </div>

        <?php if ($recherche !== '' || $filiere !== ''): ?>
        <div class="form-group">
            <label>&nbsp;</label>
            <a href="/etudiants/liste.php" class="btn btn-secondary">Réinitialiser</a>
        </div>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <?php if (empty($etudiants)): ?>
        <p class="text-muted">Aucun étudiant trouvé.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>E-mail</th>
                    <th>Date de naissance</th>
                    <th>Filière</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etudiants as $e): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['nom']) ?></td>
                    <td><?= htmlspecialchars($e['prenom']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><?= htmlspecialchars($e['date_naissance']) ?></td>
                    <td><?= htmlspecialchars($e['filiere']) ?></td>
                    <td>
                        <div class="actions">
                            <a href="/etudiants/modifier.php?id=<?= $e['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="/notes/liste.php?etudiant_id=<?= $e['id'] ?>" class="btn btn-secondary btn-sm">Notes</a>
                            <a href="/etudiants/supprimer.php?id=<?= $e['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-muted mt-1"><?= count($etudiants) ?> étudiant(s) trouvé(s).</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

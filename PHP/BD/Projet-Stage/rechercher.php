<?php
require_once 'dbconfig.php';

$soutenances = [];
$message     = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Liste des enseignants pour la liste déroulante
    $enseignants = $pdo->query("SELECT Matricule, nom_Ens, prenom_Ens FROM Enseignant ORDER BY nom_Ens")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Matricule'])) {
        $Matricule = intval($_POST['Matricule']);

        $stmt = $pdo->prepare("
            SELECT s.Numjury, s.date_soutenance, s.note,
                   e.nom AS nom_etudiant, e.prenom AS prenom_etudiant, e.classe,
                   ens.nom_Ens, ens.prenom_Ens
            FROM Soutenance s
            JOIN Etudiant e   ON s.NCE = e.NCE
            JOIN Enseignant ens ON s.Matricule = ens.Matricule
            WHERE s.date_soutenance = '15/12/2019'
              AND s.Matricule = :Matricule
            ORDER BY s.Numjury
        ");
        $stmt->execute([':Matricule' => $Matricule]);
        $soutenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($soutenances)) {
            $message = "<p class='error'>Aucune soutenance trouvée pour cet enseignant le 15/12/2019.</p>";
        }
    }

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rechercher des soutenances</title>
</head>
<body>
<div class="container">
    <h2>Soutenances du 15/12/2019</h2>

    <form method="POST" action="rechercher.php">
        <label>Choisir un enseignant :</label>
        <select name="Matricule" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($enseignants as $ens): ?>
                <option value="<?= $ens['Matricule'] ?>"
                    <?= (isset($_POST['Matricule']) && $_POST['Matricule'] == $ens['Matricule']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ens['nom_Ens'] . ' ' . $ens['prenom_Ens']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Rechercher</button>
    </form>

    <?= $message ?>

    <?php if (!empty($soutenances)): ?>
    <h3>Résultats :</h3>
    <table>
        <thead>
            <tr>
                <th>N° Jury</th>
                <th>Date</th>
                <th>Étudiant</th>
                <th>Classe</th>
                <th>Note</th>
                <th>Enseignant</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($soutenances as $s): ?>
            <tr>
                <td><?= $s['Numjury'] ?></td>
                <td><?= htmlspecialchars($s['date_soutenance']) ?></td>
                <td><?= htmlspecialchars($s['nom_etudiant'] . ' ' . $s['prenom_etudiant']) ?></td>
                <td><?= htmlspecialchars($s['classe']) ?></td>
                <td><?= $s['note'] ?>/20</td>
                <td><?= htmlspecialchars($s['nom_Ens'] . ' ' . $s['prenom_Ens']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <a href="index.php">← Retour à l'accueil</a>
</div>
</body>
</html>

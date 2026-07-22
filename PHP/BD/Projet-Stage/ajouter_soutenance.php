<?php
require_once 'dbconfig.php';

$message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer la liste des étudiants
    $etudiants = $pdo->query("SELECT NCE, nom, prenom FROM Etudiant ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer la liste des enseignants
    $enseignants = $pdo->query("SELECT Matricule, nom_Ens, prenom_Ens FROM Enseignant ORDER BY nom_Ens")->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date_soutenance = trim($_POST['date_soutenance']);
        $note            = trim($_POST['note']);
        $NCE             = intval($_POST['NCE']);
        $Matricule       = intval($_POST['Matricule']);

        if ($date_soutenance && $note !== '' && $NCE && $Matricule) {
            $stmt = $pdo->prepare("INSERT INTO Soutenance (date_soutenance, note, NCE, Matricule)
                                   VALUES (:date_soutenance, :note, :NCE, :Matricule)");
            $stmt->execute([
                ':date_soutenance' => $date_soutenance,
                ':note'            => $note,
                ':NCE'             => $NCE,
                ':Matricule'       => $Matricule
            ]);
            $message = "<p class='success'>✔ Soutenance ajoutée avec succès !</p>";
        } else {
            $message = "<p class='error'>⚠ Veuillez remplir tous les champs.</p>";
        }
    }
} catch (PDOException $e) {
    $message = "<p class='error'>Erreur : " . $e->getMessage() . "</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une soutenance</title>
</head>
<body>
<div class="container">
    <h2>Ajouter une soutenance</h2>
    <?= $message ?>
    <form method="POST" action="ajouter_soutenance.php">

        <label>Date de soutenance :</label>
        <input type="text" name="date_soutenance" placeholder="JJ/MM/AAAA" required>

        <label>Note :</label>
        <input type="number" name="note" step="0.25" min="0" max="20" required>

        <label>Étudiant :</label>
        <select name="NCE" required>
            <option value="">-- Choisir un étudiant --</option>
            <?php foreach ($etudiants as $e): ?>
                <option value="<?= $e['NCE'] ?>">
                    <?= htmlspecialchars($e['nom'] . ' ' . $e['prenom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Enseignant (jury) :</label>
        <select name="Matricule" required>
            <option value="">-- Choisir un enseignant --</option>
            <?php foreach ($enseignants as $ens): ?>
                <option value="<?= $ens['Matricule'] ?>">
                    <?= htmlspecialchars($ens['nom_Ens'] . ' ' . $ens['prenom_Ens']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Ajouter</button>
    </form>
    <a href="index.php">← Retour à l'accueil</a>
</div>
</body>
</html>

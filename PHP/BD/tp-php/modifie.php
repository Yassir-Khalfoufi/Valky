<?php 
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erreur : Aucun identifiant d'étudiant fourni.");
}

$id_etudiant = $_GET['id'];

$host = 'localhost';
$dbname = 'etudiants';
$username = 'root';
$password = 'root';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
$sql = "SELECT * FROM etudiants WHERE id = :id";
$requete = $pdo->prepare($sql);
 //execute
$requete->execute([':id' => $id_etudiant]);
$etudiant = $requete->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    die("Erreur : Aucun étudiant trouvé avec cet ID.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>modification des étudiants</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; margin: auto; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"] { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 15px; background-color: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Modifier les informations de étudiant</h2>

    <form action="traitement_modification.php" method="POST">
        
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($etudiant['id']); ?>">

        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($etudiant['nom']); ?>" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($etudiant['prenom']); ?>" required>

        <label for="date_naissance">Date de naissance :</label>
        <input type="date" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($etudiant['date_naissance']); ?>" required>

        <label for="adresse">Adresse :</label>
        <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($etudiant['adresse']); ?>" required>

        <label for="filiere">Filière :</label>
        <input type="text" id="filiere" name="filiere" value="<?php echo htmlspecialchars($etudiant['filiere']); ?>" required>

        <label for="niveau">Niveau :</label>
        <input type="text" id="niveau" name="niveau" value="<?php echo htmlspecialchars($etudiant['niveau']); ?>" required>

        <button type="submit">Enregistrer les modifications</button>
        
    </form>

</body>
</html>
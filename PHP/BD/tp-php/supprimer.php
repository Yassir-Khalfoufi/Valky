<?php
require("connexion.php");

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM etudiants WHERE id = :id");
        $stmt->execute([
            ':id' => $id
        ]);

        header("Location: liste.php?message=supprime");
        exit();

    } else {
        echo "ID manquant !";
    }
?>

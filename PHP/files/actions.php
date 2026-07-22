<?php
// actions.php — Gestion de toutes les actions POST
require_once 'db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pdo    = getPDO();

try {
    switch ($action) {

        // ── Ajouter un film ──────────────────────────────────────────
        case 'add':
            $titre = trim($_POST['titre'] ?? '');
            if ($titre === '') {
                echo json_encode(['ok' => false, 'msg' => 'Le titre est obligatoire.']);
                exit;
            }
            $stmt = $pdo->prepare('
                INSERT INTO films (titre, realisateur, annee, genre, statut, affiche_url)
                VALUES (:titre, :real, :annee, :genre, :statut, :affiche)
            ');
            $stmt->execute([
                ':titre'   => $titre,
                ':real'    => trim($_POST['realisateur'] ?? '') ?: null,
                ':annee'   => intval($_POST['annee'] ?? 0) ?: null,
                ':genre'   => trim($_POST['genre'] ?? '') ?: null,
                ':statut'  => $_POST['statut'] ?? 'a_voir',
                ':affiche' => trim($_POST['affiche_url'] ?? '') ?: null,
            ]);
            echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId(), 'msg' => 'Film ajouté !']);
            break;

        // ── Mettre à jour le statut ──────────────────────────────────
        case 'update_statut':
            $id     = intval($_POST['id'] ?? 0);
            $statut = $_POST['statut'] ?? 'a_voir';
            if (!in_array($statut, ['a_voir', 'en_cours', 'vu'])) {
                echo json_encode(['ok' => false, 'msg' => 'Statut invalide.']);
                exit;
            }
            $pdo->prepare('UPDATE films SET statut = :s WHERE id = :id')
                ->execute([':s' => $statut, ':id' => $id]);
            echo json_encode(['ok' => true, 'msg' => 'Statut mis à jour.']);
            break;

        // ── Noter un film ────────────────────────────────────────────
        case 'rate':
            $id   = intval($_POST['id'] ?? 0);
            $note = intval($_POST['note'] ?? 0);
            if ($note < 1 || $note > 5) {
                echo json_encode(['ok' => false, 'msg' => 'Note invalide (1-5).']);
                exit;
            }
            $pdo->prepare('UPDATE films SET note = :n, statut = IF(statut = "a_voir", "vu", statut) WHERE id = :id')
                ->execute([':n' => $note, ':id' => $id]);
            echo json_encode(['ok' => true, 'msg' => 'Note enregistrée.']);
            break;

        // ── Sauvegarder un commentaire ───────────────────────────────
        case 'comment':
            $id  = intval($_POST['id'] ?? 0);
            $txt = trim($_POST['commentaire'] ?? '');
            $pdo->prepare('UPDATE films SET commentaire = :c WHERE id = :id')
                ->execute([':c' => $txt ?: null, ':id' => $id]);
            echo json_encode(['ok' => true, 'msg' => 'Commentaire sauvegardé.']);
            break;

        // ── Supprimer un film ────────────────────────────────────────
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM films WHERE id = :id')->execute([':id' => $id]);
            echo json_encode(['ok' => true, 'msg' => 'Film supprimé.']);
            break;

        // ── Récupérer la liste (AJAX refresh) ───────────────────────
        case 'list':
            $filtre  = $_GET['filtre']  ?? 'tous';
            $search  = trim($_GET['q']  ?? '');
            $orderBy = $_GET['order']   ?? 'created_at';

            $allowed_order = ['titre', 'annee', 'note', 'created_at', 'statut'];
            if (!in_array($orderBy, $allowed_order)) $orderBy = 'created_at';

            $where  = [];
            $params = [];
            if ($filtre !== 'tous') {
                $where[]           = 'statut = :statut';
                $params[':statut'] = $filtre;
            }
            if ($search !== '') {
                $where[]      = '(titre LIKE :q OR realisateur LIKE :q)';
                $params[':q'] = "%$search%";
            }
            $sql = 'SELECT * FROM films' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY $orderBy DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['ok' => true, 'films' => $stmt->fetchAll()]);
            break;

        default:
            echo json_encode(['ok' => false, 'msg' => 'Action inconnue.']);
    }
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'msg' => 'Erreur BDD : ' . $e->getMessage()]);
}

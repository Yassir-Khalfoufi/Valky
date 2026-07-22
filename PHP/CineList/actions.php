<?php
// actions.php — Gestion de toutes les actions POST
require_once 'auth.php';

header('Content-Type: application/json');

// Vérification session
if (empty($_SESSION['user_id'])) {
    if (!empty($_COOKIE[REMEMBER_COOKIE])) {
        $u = checkRememberCookie($_COOKIE[REMEMBER_COOKIE]);
        if ($u) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $u['id'];
            $_SESSION['username'] = $u['username'];
        }
    }
    if (empty($_SESSION['user_id'])) {
        echo json_encode(['ok' => false, 'msg' => 'Non authentifié.', 'redirect' => 'login.php']);
        exit;
    }
}

$uid    = (int) $_SESSION['user_id'];
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

            // Vérifier si le film existe déjà pour cet utilisateur
            $check = $pdo->prepare('SELECT id FROM films WHERE LOWER(titre) = LOWER(:titre) AND user_id = :uid LIMIT 1');
            $check->execute([':titre' => $titre, ':uid' => $uid]);
            if ($check->fetch()) {
                echo json_encode(['ok' => false, 'msg' => 'Ce film est déjà dans ta collection !']);
                exit;
            }

            // Lecture de l'affiche uploadée
            $afficheData = null;
            $afficheMime = null;
            if (!empty($_FILES['affiche']['tmp_name']) && $_FILES['affiche']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                $mime    = mime_content_type($_FILES['affiche']['tmp_name']);
                if (!in_array($mime, $allowed)) {
                    echo json_encode(['ok' => false, 'msg' => 'Format image non supporté (jpg/png/webp/gif).']);
                    exit;
                }
                if ($_FILES['affiche']['size'] > 3 * 1024 * 1024) {
                    echo json_encode(['ok' => false, 'msg' => 'Image trop lourde (max 3 Mo).']);
                    exit;
                }
                $afficheData = file_get_contents($_FILES['affiche']['tmp_name']);
                $afficheMime = $mime;
            }

            $stmt = $pdo->prepare('
                INSERT INTO films (titre, realisateur, annee, genre, statut, affiche, affiche_mime, user_id)
                VALUES (:titre, :real, :annee, :genre, :statut, :affiche, :mime, :uid)
            ');
            $stmt->bindValue(':titre',   $titre);
            $stmt->bindValue(':real',    trim($_POST['realisateur'] ?? '') ?: null);
            $stmt->bindValue(':annee',   intval($_POST['annee'] ?? 0) ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':genre',   trim($_POST['genre'] ?? '') ?: null);
            $stmt->bindValue(':statut',  $_POST['statut'] ?? 'a_voir');
            $stmt->bindValue(':affiche', $afficheData, PDO::PARAM_LOB);
            $stmt->bindValue(':mime',    $afficheMime);
            $stmt->bindValue(':uid',     $uid, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId(), 'msg' => 'Film ajouté !']);
            break;

        // ── Éditer un film ──────────────────────────────────────────
        case 'edit':
            $id        = intval($_POST['id'] ?? 0);
            $titre     = trim($_POST['titre'] ?? '');
            $real      = trim($_POST['realisateur'] ?? '') ?: null;
            $annee     = intval($_POST['annee'] ?? 0) ?: null;
            $genre     = trim($_POST['genre'] ?? '') ?: null;

            if ($titre === '') {
                echo json_encode(['ok' => false, 'msg' => 'Le titre est obligatoire.']);
                exit;
            }

            // Vérifier que le film appartient à l'utilisateur
            $check = $pdo->prepare('SELECT id FROM films WHERE id = :id AND user_id = :uid LIMIT 1');
            $check->execute([':id' => $id, ':uid' => $uid]);
            if (!$check->fetch()) {
                echo json_encode(['ok' => false, 'msg' => 'Film non trouvé.']);
                exit;
            }

            // Vérifier doublon (même titre, autre ID, même user)
            $dup = $pdo->prepare('SELECT id FROM films WHERE LOWER(titre) = LOWER(:titre) AND user_id = :uid AND id != :id LIMIT 1');
            $dup->execute([':titre' => $titre, ':uid' => $uid, ':id' => $id]);
            if ($dup->fetch()) {
                echo json_encode(['ok' => false, 'msg' => 'Ce titre existe déjà dans ta collection.']);
                exit;
            }

            // Gestion nouvelle affiche si uploadée
            $afficheData = null;
            $afficheMime = null;
            if (!empty($_FILES['affiche']['tmp_name']) && $_FILES['affiche']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                $mime    = mime_content_type($_FILES['affiche']['tmp_name']);
                if (!in_array($mime, $allowed)) {
                    echo json_encode(['ok' => false, 'msg' => 'Format image non supporté.']);
                    exit;
                }
                if ($_FILES['affiche']['size'] > 3 * 1024 * 1024) {
                    echo json_encode(['ok' => false, 'msg' => 'Image trop lourde (max 3 Mo).']);
                    exit;
                }
                $afficheData = file_get_contents($_FILES['affiche']['tmp_name']);
                $afficheMime = $mime;
            }

            // Update
            if ($afficheData) {
                $stmt = $pdo->prepare('
                    UPDATE films 
                    SET titre = :titre, realisateur = :real, annee = :annee, genre = :genre,
                        affiche = :affiche, affiche_mime = :mime
                    WHERE id = :id AND user_id = :uid
                ');
                $stmt->bindValue(':affiche', $afficheData, PDO::PARAM_LOB);
                $stmt->bindValue(':mime',    $afficheMime);
            } else {
                $stmt = $pdo->prepare('
                    UPDATE films 
                    SET titre = :titre, realisateur = :real, annee = :annee, genre = :genre
                    WHERE id = :id AND user_id = :uid
                ');
            }
            $stmt->bindValue(':titre',   $titre);
            $stmt->bindValue(':real',    $real);
            $stmt->bindValue(':annee',   $annee, PDO::PARAM_INT);
            $stmt->bindValue(':genre',   $genre);
            $stmt->bindValue(':id',      $id, PDO::PARAM_INT);
            $stmt->bindValue(':uid',     $uid, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['ok' => true, 'msg' => 'Film mis à jour.']);
            break;

        // ── Mettre à jour le statut ──────────────────────────────────
        case 'update_statut':
            $id     = intval($_POST['id'] ?? 0);
            $statut = $_POST['statut'] ?? 'a_voir';
            if (!in_array($statut, ['a_voir', 'en_cours', 'vu'])) {
                echo json_encode(['ok' => false, 'msg' => 'Statut invalide.']);
                exit;
            }
            $pdo->prepare('UPDATE films SET statut = :s WHERE id = :id AND user_id = :uid')
                ->execute([':s' => $statut, ':id' => $id, ':uid' => $uid]);
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
            $pdo->prepare('UPDATE films SET note = :n, statut = IF(statut = "a_voir", "vu", statut) WHERE id = :id AND user_id = :uid')
                ->execute([':n' => $note, ':id' => $id, ':uid' => $uid]);
            echo json_encode(['ok' => true, 'msg' => 'Note enregistrée.']);
            break;

        // ── Sauvegarder un commentaire ───────────────────────────────
        case 'comment':
            $id  = intval($_POST['id'] ?? 0);
            $txt = trim($_POST['commentaire'] ?? '');
            $pdo->prepare('UPDATE films SET commentaire = :c WHERE id = :id AND user_id = :uid')
                ->execute([':c' => $txt ?: null, ':id' => $id, ':uid' => $uid]);
            echo json_encode(['ok' => true, 'msg' => 'Commentaire sauvegardé.']);
            break;

        // ── Supprimer un film ────────────────────────────────────────
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM films WHERE id = :id AND user_id = :uid')
                ->execute([':id' => $id, ':uid' => $uid]);
            echo json_encode(['ok' => true, 'msg' => 'Film supprimé.']);
            break;

        // ── Récupérer la liste (AJAX refresh) ───────────────────────
        case 'list':
            $filtre  = $_GET['filtre']  ?? 'tous';
            $search  = trim($_GET['q']  ?? '');
            $orderBy = $_GET['order']   ?? 'created_at';

            $allowed_order = ['titre', 'annee', 'note', 'created_at', 'statut'];
            if (!in_array($orderBy, $allowed_order)) $orderBy = 'created_at';

            // Toujours filtrer par user_id
            $where  = ['user_id = :uid'];
            $params = [':uid' => $uid];

            if ($filtre !== 'tous') {
                $where[]           = 'statut = :statut';
                $params[':statut'] = $filtre;
            }
            if ($search !== '') {
                $where[]      = '(titre LIKE :q OR realisateur LIKE :q)';
                $params[':q'] = "%$search%";
            }
            $sql = 'SELECT id, titre, realisateur, annee, genre, statut, note, commentaire,
                           (affiche IS NOT NULL) AS has_poster, created_at, updated_at
                    FROM films WHERE ' . implode(' AND ', $where) . " ORDER BY $orderBy DESC";
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

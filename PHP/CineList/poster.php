<?php
// poster.php — Sert l'affiche BLOB depuis la BDD
require_once 'auth.php';
$user = requireLogin();

$id  = intval($_GET['id'] ?? 0);
$uid = (int) $user['id'];

if ($id <= 0) { http_response_code(400); exit; }

$pdo  = getPDO();
$stmt = $pdo->prepare('SELECT affiche, affiche_mime FROM films WHERE id = :id AND user_id = :uid');
$stmt->execute([':id' => $id, ':uid' => $uid]);
$row  = $stmt->fetch();

if (!$row || empty($row['affiche'])) { http_response_code(404); exit; }

header('Content-Type: ' . ($row['affiche_mime'] ?: 'image/jpeg'));
header('Cache-Control: private, max-age=86400');
echo $row['affiche'];

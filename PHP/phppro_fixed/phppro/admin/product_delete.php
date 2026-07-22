<?php
// admin/product_delete.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['flash'] = 'ERR:Invalid product ID.';
    header('Location: index.php'); exit;
}

try {
    $pdo  = getPDO();
    $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['flash'] = 'ERR:Product not found.';
    } else {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        $_SESSION['flash'] = "Product \"" . $product['name'] . "\" deleted.";
    }
} catch (PDOException $e) {
    $_SESSION['flash'] = 'ERR:Could not delete product: ' . $e->getMessage();
}

header('Location: index.php');
exit;
?>

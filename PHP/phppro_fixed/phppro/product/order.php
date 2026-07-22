<?php
// order.php  — handles "SHOP NOW!" form submissions from all product detail pages
session_start();
require_once __DIR__ . '/db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=1');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../home.html');
    exit;
}

$user_id    = (int) $_SESSION['user_id'];
$product_id = (int) ($_POST['product_id'] ?? 0);
$size       = trim($_POST['size']       ?? '');
$quantity   = max(1, (int) ($_POST['quantity'] ?? 1));
$card_raw   = preg_replace('/\D/', '', $_POST['card'] ?? '');

$errors = [];

if ($product_id <= 0)       $errors[] = "Invalid product.";
if ($size === '')            $errors[] = "Please select a size.";
if (strlen($card_raw) < 13 || strlen($card_raw) > 19)
                             $errors[] = "Please enter a valid card number (13–19 digits).";

if (empty($errors)) {
    try {
        $pdo = getPDO();

        // Fetch product price
        $stmt = $pdo->prepare("SELECT price, name FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            $errors[] = "Product not found.";
        } else {
            $total     = $product['price'] * $quantity;
            $card_last4 = substr($card_raw, -4);   // store last 4 only

            $ins = $pdo->prepare(
                "INSERT INTO orders (user_id, product_id, size, quantity, total, card_last4, status)
                 VALUES (?, ?, ?, ?, ?, ?, 'paid')"
            );
            $ins->execute([$user_id, $product_id, $size, $quantity, $total, $card_last4]);

            $order_id = $pdo->lastInsertId();

            // Redirect to confirmation
            header("Location: order_confirm.php?id=" . $order_id);
            exit;
        }
    } catch (PDOException $e) {
        $errors[] = "Database error. Please try again.";
    }
}

// On error, go back with message in session
$_SESSION['order_error'] = implode(' ', $errors);
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../home.html'));
exit;
?>

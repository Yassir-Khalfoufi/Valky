<?php
// product/order_confirm.php
session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$order_id = (int)($_GET['id'] ?? 0);
$order = null;

if ($order_id > 0) {
    try {
        $pdo  = getPDO();
        $stmt = $pdo->prepare(
            "SELECT o.*, p.name AS product_name, p.image
             FROM orders o
             JOIN products p ON p.id = o.product_id
             WHERE o.id = ? AND o.user_id = ?
             LIMIT 1"
        );
        $stmt->execute([$order_id, $_SESSION['user_id']]);
        $order = $stmt->fetch();
    } catch (PDOException $e) {
        // leave $order null
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Confirmed – The Vintage Style</title>
  <link rel="stylesheet" href="shop.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
  <style>
    .confirm-box {
      max-width: 520px; margin: 80px auto; background: #fff;
      border: 1px solid #ddd; border-radius: 10px; padding: 40px;
      text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,.08);
    }
    .confirm-box .icon-check { font-size: 3.5rem; color: #27ae60; margin-bottom: 16px; }
    .confirm-box h2 { margin-bottom: 8px; }
    .confirm-box table { width: 100%; text-align: left; margin: 20px 0; border-collapse: collapse; }
    .confirm-box td { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .confirm-box td:first-child { color: #888; width: 40%; }
    .confirm-box .btn-home {
      display: inline-block; margin-top: 20px; padding: 12px 30px;
      background: #222; color: #fff; text-decoration: none;
      border-radius: 6px; font-weight: 600;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo"><p>THE <br><span>VINTAGE</span> <br>STYLE</p></div>
    <ul class="menu"><li><a href="../home.html">Home</a></li></ul>
  </header>

  <div class="confirm-box">
    <?php if ($order): ?>
      <div class="icon-check"><i class="fa-solid fa-circle-check"></i></div>
      <h2>Order Confirmed!</h2>
      <p>Thank you, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>. Your order has been placed.</p>
      <table>
        <tr><td>Order #</td> <td><?= $order['id'] ?></td></tr>
        <tr><td>Product</td> <td><?= htmlspecialchars($order['product_name']) ?></td></tr>
        <tr><td>Size</td>    <td><?= htmlspecialchars($order['size']) ?></td></tr>
        <tr><td>Qty</td>     <td><?= $order['quantity'] ?></td></tr>
        <tr><td>Total</td>   <td><strong>$<?= number_format($order['total'], 2) ?></strong></td></tr>
        <tr><td>Card</td>    <td>**** **** **** <?= htmlspecialchars($order['card_last4']) ?></td></tr>
        <tr><td>Status</td>  <td><?= ucfirst($order['status']) ?></td></tr>
      </table>
      <a class="btn-home" href="../home.html">← Continue Shopping</a>
    <?php else: ?>
      <div class="icon-check" style="color:#e74c3c"><i class="fa-solid fa-circle-xmark"></i></div>
      <h2>Order Not Found</h2>
      <p>We could not find this order.</p>
      <a class="btn-home" href="../home.html">← Back to Home</a>
    <?php endif; ?>
  </div>

  <footer class="mini-footer">
    <p>&copy; 2025 The Vintage Style. All rights reserved.</p>
  </footer>
</body>
</html>

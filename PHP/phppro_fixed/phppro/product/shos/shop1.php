<?php
// product/shos/shop1.php — example PHP version of a shop detail page
session_start();
require_once __DIR__ . '/../../db.php';

// Read any order error set by order.php
$order_error = '';
if (isset($_SESSION['order_error'])) {
    $order_error = $_SESSION['order_error'];
    unset($_SESSION['order_error']);
}

// Fetch product from DB  (shoes shop1 → slug='shop1', category='shoes')
$pdo   = getPDO();
$stmt  = $pdo->prepare("SELECT * FROM products WHERE category='shoes' AND slug='shop1' LIMIT 1");
$stmt->execute();
$product = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($product['name'] ?? 'Product') ?> – The Vintage Style</title>
    <link rel="stylesheet" href="../shop.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <style>
      .msg-error { background:#fde8e8; color:#c0392b; padding:10px 15px; border-radius:6px; margin:10px 0; }
      .msg-login { background:#fff3cd; color:#856404; padding:10px 15px; border-radius:6px; margin:10px 0; }
    </style>
  </head>
  <body>
    <header>
      <div>
        <img class="logoo" src="../../image/#L01f525 ORDER NOW FOR CUSTOM LOGO DESIGN! #L01f525.jpg" alt="">
      </div>
      <div class="logo"><p>THE <br><span>VINTAGE</span> <br>STYLE</p></div>
      <ul class="menu">
        <li><a href="../../home.html">Home</a></li>
        <li class="dropdown"><a href="#">Product</a>
          <ul class="submenu">
            <li><a href="../jakect .html">Jacket</a></li>
            <li><a href="../Pants.html">Pants</a></li>
            <li><a href="../Hoodie.html">Hoodie</a></li>
            <li><a href="../Shoes.html">Shoes</a></li>
          </ul>
        </li>
        <li><a href="../../home.html#about">About</a></li>
        <li><a href="../../home.html#contact">Contact</a></li>
      </ul>
      <div class="icon">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span style="color:#fff;font-size:.85rem"><?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="../../logout.php" style="color:#ccc;font-size:.8rem;margin-left:8px">Logout</a>
        <?php else: ?>
          <a href="../../login.php"><i class="fa-solid fa-user icon2"></i></a>
        <?php endif; ?>
      </div>
    </header>

    <main>
      <?php if ($order_error !== ''): ?>
        <div class="msg-error" style="max-width:800px;margin:20px auto"><?= htmlspecialchars($order_error) ?></div>
      <?php endif; ?>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="msg-login" style="max-width:800px;margin:20px auto">
          Please <a href="../../login.php">log in</a> to place an order.
        </div>
      <?php endif; ?>

      <div class="shop">
        <div>
          <img src="../../<?= htmlspecialchars($product['image'] ?? 'image/3.1.avif') ?>" alt="" width="400px" height="400px">
        </div>
        <div class="shopinfo">
          <h1 id="title"><?= htmlspecialchars($product['name'] ?? '') ?></h1>
          <h3>
            <?php if ($product['old_price']): ?>
              <del>$<?= number_format($product['old_price'], 2) ?></del>
            <?php endif; ?>
            <span><b>$<?= number_format($product['price'], 2) ?></b></span>
          </h3>

          <form action="../order.php" method="POST">
            <!-- Hidden fields tell order.php which product this is -->
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

            <label for="size">Size</label>
            <select name="size" id="size">
              <option value="39">39</option>
              <option value="40">40</option>
              <option value="41">41</option>
              <option value="42">42</option>
              <option value="43">43</option>
            </select>

            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" style="width:60px">

            <label for="payement">Payment</label><br>
            <i class="fa-brands fa-cc-mastercard"></i>
            <i class="fa-brands fa-paypal"></i>
            <i class="fa-brands fa-cc-apple-pay"></i><br>
            <input type="text" name="card" id="card" required
                   maxlength="19" placeholder="Enter your card number"><br>

            <button class="buttonshop" type="submit">SHOP NOW!</button>
          </form>

          <h3>Description</h3>
          <p>
            Step back in time with these vintage-inspired shoes. Featuring classic stitching,
            a timeless silhouette, and a comfortable cushioned sole.
            <ul>
              <li>Material: Synthetic leather</li>
              <li>Available sizes: 39–43</li>
              <li>Product weight: 450g</li>
            </ul>
            <h2>High Quality in The Vintage Style</h2>
          </p>
        </div>
      </div>

      <div class="delivery">
        <div><i class="fa-solid fa-truck"></i><p>Fast Delivery in 7 days</p></div>
        <div><i class="fa-solid fa-shirt"></i><p>High-Quality Products</p></div>
        <div><i class="fa-solid fa-tag"></i><p>Weekly Discounts Up To 70%</p></div>
      </div>

      <div id="about">
        <section class="abt vintage">
          <div class="abt-content">
            <h2>About Our Vintage Collection</h2>
            <p>Inspired by timeless traditions and classic craftsmanship, our vintage collection reflects elegance, authenticity, and character.</p>
          </div>
        </section>
      </div>

      <div id="contact">
        <section class="contact vintage">
          <h2>Get in Touch</h2>
          <div class="contact-container">
            <div class="contact-info">
              <p><strong>📍 Location:</strong> Morocco</p>
              <p><strong>📞 Phone:</strong> +212 6 00 00 00 00</p>
              <p><strong>✉ Email:</strong> vintage@gmail.com</p>
            </div>
            <form class="contact-form" action="../../contact.php" method="POST">
              <input type="text" name="name" placeholder="Your Name" required>
              <input type="email" name="email" placeholder="Your Email" required>
              <textarea name="message" placeholder="Your Message" required></textarea>
              <button type="submit">Send Message</button>
            </form>
          </div>
        </section>
      </div>
    </main>

    <footer class="mini-footer">
      <p>&copy; 2025 The Vintage Style. All rights reserved.</p>
    </footer>
  </body>
</html>

<?php
// signup.php
session_start();
require 'db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $password =       $_POST['password'] ?? '';

    // Basic validation
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Username must be between 3 and 50 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        try {
            $pdo  = getPDO();
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$username, $email, $phone, $hash]);

            $success = true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {          // duplicate entry
                $errors[] = "Username or email already taken. Please choose another.";
            } else {
                $errors[] = "Database error. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up – The Vintage Style</title>
    <link rel="stylesheet" href="sign.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet" />
    <style>
      .msg-error { background:#fde8e8; color:#c0392b; padding:10px 15px; border-radius:6px; margin-bottom:12px; font-size:.9rem; }
      .msg-success{ background:#e8f8e8; color:#27ae60; padding:10px 15px; border-radius:6px; margin-bottom:12px; font-size:.9rem; }
    </style>
  </head>
  <body>
     <header>
            <div>
                    <img class="logoo" src="image/#L01f525 ORDER NOW FOR CUSTOM LOGO DESIGN! #L01f525.jpg" alt="">
             </div>
                   <div class="logo">
                        <p>THE <br><span>VINTAGE</span> <br>STYLE</p>
                     </div>
        <ul class="menu">
            <li><a href="home.html">Home</a></li>
            <li class="dropdown"><a href="#">Product</a>
                  <ul class="submenu">
                <li><a href="product/jakect .html">Jacket</a></li>
                <li><a href="product/Pants.html">Pants</a></li>
                <li><a href="product/Hoodie.html">Hoodie</a></li>
                <li><a href="product/Shoes.html">Shoes</a></li>
              </ul></li>
            <li><a href="home.html#about">About</a></li>
            <li><a href="home.html#contact">Contact</a></li>
        </ul>
        <div class="icon">
            <a href="login.php"><i class="fa-solid fa-user icon2"></i></a>
        </div>
    </header>

    <div class="home">
      <div class="login-menu">
        <?php if ($success): ?>
          <div class="msg-success">
            Account created successfully! <a href="login.php">Log in now →</a>
          </div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <div class="msg-error">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
          </div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
          <h2>
            Welcome!<br />
            <span>Already have an account? <a href="login.php">Log in</a></span>
          </h2>
          <div class="data">
            <label for="username">Username</label><br />
            <input type="text" name="username" id="username" required
                   maxlength="50" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" /><br />

            <label for="email">E-mail</label><br />
            <input type="email" name="email" id="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" /><br />

            <label for="phone">Phone Number</label><br />
            <input type="tel" name="phone" id="phone" maxlength="20"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" /><br />

            <label for="password">Password</label><br />
            <input type="password" name="password" id="password" required minlength="6" maxlength="100" />
          </div>
          <div class="passwordforget">
            <button type="submit">Sign Up</button>
          </div>
        </form>
      </div>
      <div class="signup-background">
        <img src="image/signupbackground.png" alt="background" width="900px" />
      </div>
    </div>

    <footer class="mini-footer">
      <p>&copy; 2025 The Vintage Style. All rights reserved.</p>
    </footer>
  </body>
</html>

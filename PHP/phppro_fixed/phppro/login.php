<?php
// login.php
session_start();
require 'db.php';

// Already logged in → redirect home
if (isset($_SESSION['user_id'])) {
    header('Location: home.html');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password =       $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please fill in all fields.";
    } else {
        try {
            $pdo  = getPDO();
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: home.html');
                exit;
            } else {
                $error = "Incorrect username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login – The Vintage Style</title>
    <link rel="stylesheet" href="login.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet" />
    <style>
      .msg-error { background:#fde8e8; color:#c0392b; padding:10px 15px; border-radius:6px; margin-bottom:12px; font-size:.9rem; }
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
      <div class="login-background">
        <img src="image/loginpage.png" alt="background" width="900px" />
      </div>
      <div class="login-menu">
        <?php if ($error !== ''): ?>
          <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
          <h2>
            Welcome Back!<br />
            <span>Don't have an account? <a href="signup.php">Sign Up</a></span>
          </h2>
          <div class="data">
            <label for="username">Username</label><br />
            <input type="text" name="username" id="username" required maxlength="50"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" /><br />
            <label for="password">Password</label><br />
            <input type="password" name="password" id="password" required maxlength="100" />
          </div>
          <div class="passwordforget">
            <button type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>

    <footer class="mini-footer">
      <p>&copy; 2025 The Vintage Style. All rights reserved.</p>
    </footer>
  </body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cinema</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/cinema/public/css/style.css">
</head>
<body>
<nav class="nav">
  <a href="/cinema/movies" class="nav-logo">🎬 Cinema</a>
  <div class="nav-links">
    <a href="/cinema/movies">Films</a>
    <?php if (isset($_SESSION['user'])): ?>
      <a href="/cinema/lists">My Lists</a>
      <span class="nav-user"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
      <a href="/cinema/auth/logout">Logout</a>
    <?php else: ?>
      <a href="/cinema/auth/login">Login</a>
      <a href="/cinema/auth/register">Register</a>
    <?php endif; ?>
  </div>
</nav>
<main class="container">

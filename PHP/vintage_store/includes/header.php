<?php
// includes/header.php
require_once __DIR__ . '/../includes/auth.php';
$flash = getFlash();
$user  = currentUser();

// Cart count
$cartCount = 0;
if ($user) {
    require_once __DIR__ . '/../config/db.php';
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $cartCount = (int)($stmt->fetchColumn() ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Le Grenier Vintage' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/vintage_store/assets/css/style.css">">
</head>
<body>

<?php if ($flash): ?>
<div class="flash flash--<?= htmlspecialchars($flash['type']) ?>">
    <?= htmlspecialchars($flash['msg']) ?>
    <button class="flash__close" onclick="this.parentElement.remove()">✕</button>
</div>
<?php endif; ?>

<header class="site-header">
    <div class="header__inner">
        <a href="/index.php" class="logo">
            <span class="logo__line">Le Grenier</span>
            <span class="logo__sub">— Vintage —</span>
        </a>

        <nav class="nav">
            <a href="/index.php" class="nav__link">Shop</a>
            <a href="/index.php?cat=jackets" class="nav__link">Jackets</a>
            <a href="/index.php?cat=dresses" class="nav__link">Dresses</a>
            <a href="/index.php?cat=tops" class="nav__link">Tops</a>
            <a href="/index.php?cat=pants" class="nav__link">Pants</a>
            <a href="/index.php?cat=accessories" class="nav__link">Accessories</a>
        </nav>

        <div class="header__actions">
            <?php if ($user): ?>
                <a href="/cart.php" class="btn-icon" title="Cart">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    <?php if ($cartCount > 0): ?><span class="badge"><?= $cartCount ?></span><?php endif; ?>
                </a>
                <div class="user-menu">
                    <button class="user-menu__trigger"><?= htmlspecialchars($user['username']) ?> ▾</button>
                    <div class="user-menu__dropdown">
                        <?php if (isAdmin()): ?>
                        <a href="/admin.php">Admin Panel</a>
                        <?php endif; ?>
                        <a href="/logout.php">Log out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login.php" class="btn btn--ghost">Login</a>
                <a href="/register.php" class="btn btn--primary">Register</a>
            <?php endif; ?>
        </div>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main class="main-content">

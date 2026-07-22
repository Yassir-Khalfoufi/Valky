<?php
// cart.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    flashMessage('error', 'Please log in to access your cart.');
    header('Location: /login.php'); exit;
}

$pdo    = getDB();
$userId = $_SESSION['user_id'];

// ── Handle POST actions ──────────────────────────────────────────────────
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($action === 'add' && isset($_POST['product_id'])) {
    $pid = (int)$_POST['product_id'];
    // Check product exists and has stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $prod = $stmt->fetch();

    if ($prod && $prod['stock'] > 0) {
        // If already in cart, increment
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $pid]);
        $existing = $stmt->fetch();
        if ($existing) {
            $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?")->execute([$existing['id']]);
        } else {
            $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)")->execute([$userId, $pid]);
        }
        flashMessage('success', 'Added to your cart!');
    } else {
        flashMessage('error', 'Sorry, this item is out of stock.');
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php')); exit;
}

if ($action === 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
    $cartId = (int)$_GET['id'];
    $qty    = max(1, (int)$_GET['qty']);
    $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?")->execute([$qty, $cartId, $userId]);

    // Return JSON for AJAX
    $stmt = $pdo->prepare("
        SELECT SUM(c.quantity * p.price) AS total
        FROM cart c JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $total = (float)($stmt->fetchColumn() ?? 0);

    $stmt2 = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt2->execute([$userId]);
    $badge = (int)($stmt2->fetchColumn() ?? 0);

    header('Content-Type: application/json');
    echo json_encode(['total' => $total, 'badge' => $badge]);
    exit;
}

if ($action === 'remove' && isset($_POST['cart_id'])) {
    $cartId = (int)$_POST['cart_id'];
    $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([$cartId, $userId]);
    flashMessage('success', 'Item removed from cart.');
    header('Location: /cart.php'); exit;
}

if ($action === 'clear') {
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$userId]);
    flashMessage('success', 'Cart cleared.');
    header('Location: /cart.php'); exit;
}

// ── Fetch cart items ──────────────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id,
           p.name, p.price, p.size, p.era, p.image, p.stock, p.condition_label
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll();

$subtotal  = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
$itemCount = array_sum(array_column($items, 'quantity'));

$pageTitle = 'Cart — Le Grenier Vintage';
require_once __DIR__ . '/includes/header.php';
?>

<div class="cart-page">
    <h1>Your Cart
        <?php if ($itemCount > 0): ?>
        <span style="font-size:1rem;color:var(--muted);font-family:var(--ff-body);font-weight:400">
            — <?= $itemCount ?> item<?= $itemCount !== 1 ? 's' : '' ?>
        </span>
        <?php endif; ?>
    </h1>

    <?php if (empty($items)): ?>
    <div class="cart-empty">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
        </svg>
        <h2>Your cart is empty</h2>
        <p>Find something you love and it'll be waiting here.</p>
        <a href="/index.php" class="btn btn--primary" style="margin-top:1.5rem">Browse the shop</a>
    </div>

    <?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Price</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <div class="cart-table__product">
                    <div class="cart-table__thumb">
                        <?php if ($item['image']): ?>
                            <img src="/uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php else: ?>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M20.38 3.46 16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.57a1 1 0 00.99.84H5v10a2 2 0 002 2h10a2 2 0 002-2V10h1.15a1 1 0 00.99-.84l.58-3.57a2 2 0 00-1.34-2.23z"/></svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="cart-table__name"><a href="/product.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['name']) ?></a></p>
                        <p class="cart-table__meta"><?= htmlspecialchars($item['era'] ?? '') ?> · <?= htmlspecialchars($item['condition_label'] ?? '') ?></p>
                    </div>
                </div>
            </td>
            <td><?= htmlspecialchars($item['size'] ?? '—') ?></td>
            <td>
                <div class="qty-control" data-id="<?= $item['cart_id'] ?>">
                    <button data-action="minus">−</button>
                    <span><?= $item['quantity'] ?></span>
                    <button data-action="plus">+</button>
                </div>
            </td>
            <td style="font-family:var(--ff-display);font-weight:700">
                $<?= number_format($item['price'] * $item['quantity'], 2) ?>
            </td>
            <td>
                <form method="POST" action="/cart.php">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                    <button type="submit" class="remove-btn" title="Remove" data-confirm="Remove this item from your cart?">✕</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1.5rem;margin-top:1.5rem">
        <form method="POST" action="/cart.php">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="btn btn--ghost" data-confirm="Clear your entire cart?">Clear cart</button>
        </form>

        <div class="cart-summary">
            <div class="cart-summary__row">
                <span>Subtotal</span>
                <span id="cartTotal">$<?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="cart-summary__row">
                <span>Shipping</span>
                <span style="color:var(--moss)">Free</span>
            </div>
            <div class="cart-summary__row cart-summary__row--total">
                <span>Total</span>
                <span>$<?= number_format($subtotal, 2) ?></span>
            </div>
            <button class="btn btn--moss btn--full" style="margin-top:1rem" onclick="alert('Checkout flow coming soon!')">
                Checkout →
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

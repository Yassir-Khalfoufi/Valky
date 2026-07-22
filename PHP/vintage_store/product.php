<?php
// product.php — Single product page
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getDB();
$id  = (int)($_GET['id'] ?? 0);

if (!$id) { header('Location: /index.php'); exit; }

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    flashMessage('error', 'Product not found.');
    header('Location: /index.php'); exit;
}

// Related products (same category, exclude self)
$rel = [];
if ($p['category_id']) {
    $stmt2 = $pdo->prepare("
        SELECT p.*, c.name AS cat_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ? AND p.id != ?
        LIMIT 4
    ");
    $stmt2->execute([$p['category_id'], $id]);
    $rel = $stmt2->fetchAll();
}

$pageTitle = htmlspecialchars($p['name']) . ' — Le Grenier Vintage';
require_once __DIR__ . '/includes/header.php';
?>

<div class="product-detail">
    <!-- Image -->
    <div class="product-detail__image">
        <?php if ($p['image']): ?>
            <img src="/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <?php else: ?>
            <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;color:var(--sepia)">
                <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <path d="M20.38 3.46 16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.57a1 1 0 00.99.84H5v10a2 2 0 002 2h10a2 2 0 002-2V10h1.15a1 1 0 00.99-.84l.58-3.57a2 2 0 00-1.34-2.23z"/>
                </svg>
                <span style="font-size:.8rem;letter-spacing:.1em;opacity:.5;text-transform:uppercase"><?= htmlspecialchars($p['cat_name'] ?? 'Vintage') ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="product-detail__info">
        <a href="/index.php?cat=<?= htmlspecialchars($p['cat_slug'] ?? '') ?>" style="font-size:.8rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">
            ← <?= htmlspecialchars($p['cat_name'] ?? 'Shop') ?>
        </a>

        <?php if ($p['era']): ?>
            <div class="product-detail__era"><?= htmlspecialchars($p['era']) ?></div>
        <?php endif; ?>

        <h1><?= htmlspecialchars($p['name']) ?></h1>
        <p class="product-detail__price">$<?= number_format($p['price'], 2) ?></p>

        <div class="product-detail__specs">
            <?php if ($p['size']): ?>
                <span class="spec-tag"><strong>Size</strong> <?= htmlspecialchars($p['size']) ?></span>
            <?php endif; ?>
            <?php if ($p['condition_label']): ?>
                <span class="spec-tag"><strong>Condition</strong> <?= htmlspecialchars($p['condition_label']) ?></span>
            <?php endif; ?>
            <?php if ($p['era']): ?>
                <span class="spec-tag"><strong>Era</strong> <?= htmlspecialchars($p['era']) ?></span>
            <?php endif; ?>
            <span class="spec-tag"><strong>Stock</strong> <?= $p['stock'] ?></span>
        </div>

        <p class="product-detail__desc"><?= nl2br(htmlspecialchars($p['description'] ?? '')) ?></p>

        <div class="product-detail__actions">
            <?php if ($p['stock'] > 0): ?>
                <form action="/cart.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn btn--primary btn--full">Add to Cart</button>
                </form>
                <a href="/cart.php" class="btn btn--ghost btn--full">View Cart</a>
            <?php else: ?>
                <div class="product-detail__sold">
                    <p>This piece has found its new home. Check back for similar arrivals.</p>
                    <a href="/index.php?cat=<?= htmlspecialchars($p['cat_slug'] ?? '') ?>" class="btn btn--ghost" style="margin-top:.8rem">Browse similar</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Related products -->
<?php if (!empty($rel)): ?>
<div class="section">
    <div class="section__header">
        <h2>From the Same Collection</h2>
        <a href="/index.php?cat=<?= htmlspecialchars($p['cat_slug'] ?? '') ?>" class="section__see-all">See all →</a>
    </div>
    <div class="product-grid">
        <?php foreach ($rel as $r): ?>
        <article class="product-card">
            <a href="/product.php?id=<?= $r['id'] ?>" class="product-card__link" aria-label="<?= htmlspecialchars($r['name']) ?>"></a>
            <div class="product-card__image-wrap">
                <?php if ($r['image']): ?>
                    <img src="/uploads/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="product-card__placeholder">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M20.38 3.46 16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.57a1 1 0 00.99.84H5v10a2 2 0 002 2h10a2 2 0 002-2V10h1.15a1 1 0 00.99-.84l.58-3.57a2 2 0 00-1.34-2.23z"/>
                        </svg>
                    </div>
                <?php endif; ?>
                <?php if ($r['era']): ?><span class="product-card__era"><?= htmlspecialchars($r['era']) ?></span><?php endif; ?>
            </div>
            <div class="product-card__body">
                <p class="product-card__category"><?= htmlspecialchars($r['cat_name'] ?? '') ?></p>
                <h3 class="product-card__name"><?= htmlspecialchars($r['name']) ?></h3>
                <div class="product-card__footer">
                    <span class="product-card__price">$<?= number_format($r['price'], 2) ?></span>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

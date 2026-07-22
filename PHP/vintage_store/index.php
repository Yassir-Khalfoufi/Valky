<?php
// index.php — Main shop page
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Shop — Le Grenier Vintage';
$pdo = getDB();

// Resolve category filter
$catSlug = $_GET['cat'] ?? null;
$catId   = null;
$catName = 'All Pieces';

if ($catSlug) {
    $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE slug = ?");
    $stmt->execute([$catSlug]);
    $cat = $stmt->fetch();
    if ($cat) { $catId = $cat['id']; $catName = $cat['name']; }
}

// Fetch categories for filter bar
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Build product query
$sql = "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id";
$params = [];
if ($catId) { $sql .= " WHERE p.category_id = ?"; $params[] = $catId; }
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Unique eras & sizes for JS filters
$eras  = array_unique(array_filter(array_column($products, 'era')));
$sizes = array_unique(array_filter(array_column($products, 'size')));
sort($eras); sort($sizes);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero (only on main page) -->
<?php if (!$catSlug): ?>
<section class="hero">
    <p class="hero__eyebrow">New arrivals every week</p>
    <h1>Wear the Story<br><em>of Another Era</em></h1>
    <p>Carefully sourced vintage clothing from the 1960s to the 1990s. Each piece is one of a kind.</p>
    <div class="hero__cta">
        <a href="#shop" class="btn btn--ivory">Browse the Collection</a>
        <a href="/register.php" class="btn btn--outline-ivory">Create an Account</a>
    </div>
</section>
<?php endif; ?>

<!-- Filter bar -->
<div class="filter-bar">
    <div class="filter-bar__inner">
        <label>Search:</label>
        <input type="search" id="searchInput" placeholder="Search pieces…">

        <label>Era:</label>
        <select id="eraFilter">
            <option value="">All eras</option>
            <?php foreach ($eras as $e): ?>
            <option value="<?= strtolower(htmlspecialchars($e)) ?>"><?= htmlspecialchars($e) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Size:</label>
        <select id="sizeFilter">
            <option value="">All sizes</option>
            <?php foreach ($sizes as $s): ?>
            <option value="<?= strtolower(htmlspecialchars($s)) ?>"><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Category:</label>
        <select onchange="location.href=this.value">
            <option value="/index.php" <?= !$catSlug ? 'selected' : '' ?>>All</option>
            <?php foreach ($cats as $c): ?>
            <option value="/index.php?cat=<?= $c['slug'] ?>" <?= $catSlug === $c['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Product grid -->
<div class="section" id="shop">
    <div class="section__header">
        <h2><?= htmlspecialchars($catName) ?></h2>
        <span class="section__see-all"><?= count($products) ?> piece<?= count($products) !== 1 ? 's' : '' ?></span>
    </div>

    <div class="product-grid">
        <?php if (empty($products)): ?>
        <div class="no-results">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/>
                <path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/>
                <path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/>
            </svg>
            <p>No pieces found in this category yet.</p>
        </div>
        <?php else: ?>
        <?php foreach ($products as $p): ?>
        <article class="product-card"
                 data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>"
                 data-era="<?= strtolower(htmlspecialchars($p['era'] ?? '')) ?>"
                 data-size="<?= strtolower(htmlspecialchars($p['size'] ?? '')) ?>">

            <a href="/product.php?id=<?= $p['id'] ?>" class="product-card__link" aria-label="<?= htmlspecialchars($p['name']) ?>"></a>

            <div class="product-card__image-wrap">
                <?php if ($p['image']): ?>
                    <img src="/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="product-card__placeholder">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M20.38 3.46 16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.57a1 1 0 00.99.84H5v10a2 2 0 002 2h10a2 2 0 002-2V10h1.15a1 1 0 00.99-.84l.58-3.57a2 2 0 00-1.34-2.23z"/>
                        </svg>
                        <span><?= htmlspecialchars($p['cat_name'] ?? 'Vintage') ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($p['era']): ?>
                    <span class="product-card__era"><?= htmlspecialchars($p['era']) ?></span>
                <?php endif; ?>
                <?php if ($p['stock'] == 1): ?>
                    <span class="product-card__stock-low">Last one</span>
                <?php endif; ?>
            </div>

            <div class="product-card__body">
                <p class="product-card__category"><?= htmlspecialchars($p['cat_name'] ?? '') ?></p>
                <h3 class="product-card__name"><?= htmlspecialchars($p['name']) ?></h3>
                <div class="product-card__meta">
                    <?php if ($p['size']): ?><span class="product-card__tag">Size <?= htmlspecialchars($p['size']) ?></span><?php endif; ?>
                    <?php if ($p['condition_label']): ?><span class="product-card__tag"><?= htmlspecialchars($p['condition_label']) ?></span><?php endif; ?>
                </div>
                <div class="product-card__footer">
                    <span class="product-card__price">$<?= number_format($p['price'], 2) ?></span>
                    <?php if ($p['stock'] > 0): ?>
                    <form action="/cart.php" method="POST" style="position:relative;z-index:2">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn--primary">Add to cart</button>
                    </form>
                    <?php else: ?>
                    <span style="color:var(--muted);font-size:.8rem">Sold out</span>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

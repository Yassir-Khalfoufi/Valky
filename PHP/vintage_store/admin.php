<?php
// admin.php — Product management panel (admin only)
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
if (!isAdmin()) {
    flashMessage('error', 'Access denied.');
    header('Location: /index.php'); exit;
}

$pdo = getDB();

// ── Handle form actions ────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD product
    if ($action === 'add') {
        $name      = trim($_POST['name']      ?? '');
        $desc      = trim($_POST['description'] ?? '');
        $price     = (float)($_POST['price']  ?? 0);
        $era       = trim($_POST['era']       ?? '');
        $condition = trim($_POST['condition'] ?? '');
        $size      = trim($_POST['size']      ?? '');
        $catId     = (int)($_POST['category_id'] ?? 0) ?: null;
        $stock     = max(0, (int)($_POST['stock'] ?? 1));

        // Handle image upload
        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $imageName = uniqid('img_') . '.' . $ext;
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
            }
        }

        if ($name && $price > 0) {
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, era, condition_label, size, category_id, image, stock)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $desc, $price, $era, $condition, $size, $catId, $imageName, $stock]);
            flashMessage('success', 'Product "' . $name . '" added.');
        } else {
            flashMessage('error', 'Name and price are required.');
        }
        header('Location: /admin.php'); exit;
    }

    // DELETE product
    if ($action === 'delete' && isset($_POST['product_id'])) {
        $pid = (int)$_POST['product_id'];
        // Remove from cart first
        $pdo->prepare("DELETE FROM cart WHERE product_id = ?")->execute([$pid]);
        // Get image to delete
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$pid]);
        $img = $stmt->fetchColumn();
        if ($img && file_exists(__DIR__ . '/uploads/' . $img)) unlink(__DIR__ . '/uploads/' . $img);
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$pid]);
        flashMessage('success', 'Product deleted.');
        header('Location: /admin.php'); exit;
    }
}

// Fetch all products + categories
$products   = $pdo->query("
    SELECT p.*, c.name AS cat_name FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$pageTitle = 'Admin Panel — Le Grenier Vintage';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-page">
    <h1>Admin Panel</h1>

    <!-- Add product form -->
    <div class="admin-form">
        <h2>Add New Product</h2>
        <form method="POST" action="/admin.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">

            <div class="form-row">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Levi's 501 Jacket">
                </div>
                <div class="form-group">
                    <label>Price (USD) *</label>
                    <input type="number" name="price" step="0.01" min="0" required placeholder="89.00">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" placeholder="Short description of the piece">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" style="width:100%;padding:.7rem 1rem;border:1px solid var(--border);border-radius:4px;background:var(--ivory);font-size:.95rem">
                        <option value="">— Select category —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Era</label>
                    <input type="text" name="era" placeholder="e.g. 1980s">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Size</label>
                    <input type="text" name="size" placeholder="S / M / L / XL / 32 / Universal…">
                </div>
                <div class="form-group">
                    <label>Condition</label>
                    <input type="text" name="condition" placeholder="Excellent / Good / Fair">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Stock quantity</label>
                    <input type="number" name="stock" min="0" value="1">
                </div>
                <div class="form-group">
                    <label>Product image (JPG/PNG/WEBP)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                           style="padding:.5rem;font-size:.85rem">
                </div>
            </div>
            <button type="submit" class="btn btn--primary">Add Product</button>
        </form>
    </div>

    <!-- Products table -->
    <h2 style="margin-bottom:1.2rem">All Products (<?= count($products) ?>)</h2>
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Era</th>
                    <th>Size</th>
                    <th>Condition</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td style="color:var(--muted)">#<?= $p['id'] ?></td>
                <td>
                    <a href="/product.php?id=<?= $p['id'] ?>" style="color:var(--rust);font-weight:500">
                        <?= htmlspecialchars($p['name']) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($p['cat_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($p['era'] ?? '—') ?></td>
                <td><?= htmlspecialchars($p['size'] ?? '—') ?></td>
                <td><?= htmlspecialchars($p['condition_label'] ?? '—') ?></td>
                <td>
                    <span style="color:<?= $p['stock'] == 0 ? 'var(--rust)' : ($p['stock'] == 1 ? '#c47a1a' : 'var(--moss)') ?>;font-weight:500">
                        <?= $p['stock'] ?>
                    </span>
                </td>
                <td style="font-weight:600">$<?= number_format($p['price'], 2) ?></td>
                <td>
                    <form method="POST" action="/admin.php" style="display:inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="delete-btn"
                                data-confirm="Delete '<?= htmlspecialchars($p['name']) ?>'? This cannot be undone.">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

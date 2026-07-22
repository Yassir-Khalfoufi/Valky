<?php
// admin/product_form.php  — add or edit a product
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$pdo = getPDO();
$id  = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$isEdit = $id > 0;

// Load existing product for edit
$product = [
    'id' => 0, 'category' => 'shoes', 'name' => '',
    'price' => '', 'old_price' => '', 'image' => '',
    'description' => '', 'slug' => ''
];
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        $_SESSION['flash'] = 'ERR:Product not found.';
        header('Location: index.php'); exit;
    }
    $product = $row;
}

$errors = [];

// ── Handle POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category    = $_POST['category']    ?? '';
    $name        = trim($_POST['name']   ?? '');
    $price       = $_POST['price']       ?? '';
    $old_price   = trim($_POST['old_price'] ?? '') ?: null;
    $image       = trim($_POST['image']  ?? '');
    $description = trim($_POST['description'] ?? '');
    $slug        = trim($_POST['slug']   ?? '');

    // Validation
    if (!in_array($category, ['jacket','pants','hoodie','shoes']))
        $errors[] = "Please select a valid category.";
    if ($name === '')
        $errors[] = "Product name is required.";
    if (!is_numeric($price) || (float)$price <= 0)
        $errors[] = "Price must be a positive number.";
    if ($old_price !== null && (!is_numeric($old_price) || (float)$old_price <= 0))
        $errors[] = "Original price must be a positive number (or leave blank).";

    if (empty($errors)) {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare(
                    "UPDATE products
                     SET category=?, name=?, price=?, old_price=?, image=?, description=?, slug=?
                     WHERE id=?"
                );
                $stmt->execute([$category, $name, $price, $old_price, $image, $description, $slug, $id]);
                $_SESSION['flash'] = "Product \"$name\" updated successfully.";
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO products (category, name, price, old_price, image, description, slug)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$category, $name, $price, $old_price, $image, $description, $slug]);
                $_SESSION['flash'] = "Product \"$name\" added successfully.";
            }
            header('Location: index.php'); exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Keep posted values on error
    $product = array_merge($product, [
        'category' => $category, 'name' => $name, 'price' => $price,
        'old_price' => $old_price ?? '', 'image' => $image,
        'description' => $description, 'slug' => $slug
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Edit' : 'Add' ?> Product – Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root { --brown: #51351f; --brown2: #8c5d38; --border: #e8e0d6; --light: #fdf9f5; }
    body { font-family: 'Montserrat', sans-serif; background: #f5f0ea; color: #2d2d2d; }

    /* Sidebar (same as index) */
    .sidebar {
      position: fixed; top: 0; left: 0; width: 220px; height: 100vh;
      background: var(--brown); color: #fff; display: flex; flex-direction: column; z-index: 100;
    }
    .sidebar .brand { padding: 28px 20px 20px; border-bottom: 1px solid rgba(255,255,255,.15); }
    .sidebar .brand h2 { font-size: .95rem; letter-spacing: 2px; text-transform: uppercase; }
    .sidebar .brand p  { font-size: .7rem; opacity: .6; margin-top: 3px; }
    .sidebar nav { flex: 1; padding: 16px 0; }
    .sidebar nav a {
      display: flex; align-items: center; gap: 10px; padding: 11px 20px;
      color: rgba(255,255,255,.8); text-decoration: none; font-size: .85rem; font-weight: 500;
      transition: background .15s, color .15s;
    }
    .sidebar nav a:hover, .sidebar nav a.active { background: rgba(255,255,255,.12); color: #fff; }
    .sidebar nav a i { width: 18px; text-align: center; }
    .sidebar .user-box {
      padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.15);
      font-size: .78rem; display: flex; align-items: center; gap: 10px;
    }
    .sidebar .user-box .avatar {
      width: 32px; height: 32px; border-radius: 50%; background: var(--brown2);
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }
    .sidebar .user-box a { color: rgba(255,255,255,.6); text-decoration: none; font-size: .75rem; }
    .sidebar .user-box a:hover { color: #fff; }

    .main { margin-left: 220px; padding: 32px; }

    /* Breadcrumb */
    .breadcrumb { font-size: .8rem; color: #aaa; margin-bottom: 20px; }
    .breadcrumb a { color: var(--brown); text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }

    /* Form card */
    .form-card {
      background: #fff; border-radius: 14px; padding: 36px 40px;
      box-shadow: 0 2px 12px rgba(0,0,0,.07); max-width: 760px;
    }
    .form-card h2 { font-size: 1.2rem; color: var(--brown); margin-bottom: 28px; display: flex; align-items: center; gap: 10px; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    label { font-size: .78rem; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: .5px; }
    label .required { color: #e74c3c; margin-left: 3px; }
    input[type=text], input[type=number], select, textarea {
      padding: 11px 14px; border: 1.5px solid var(--border); border-radius: 8px;
      font-size: .9rem; font-family: inherit; background: var(--light);
      transition: border-color .2s;
    }
    input:focus, select:focus, textarea:focus { outline: none; border-color: var(--brown); }
    textarea { resize: vertical; min-height: 100px; }
    .hint { font-size: .75rem; color: #aaa; margin-top: 3px; }

    /* Image preview */
    .img-preview-box {
      border: 2px dashed var(--border); border-radius: 10px; padding: 16px;
      display: flex; align-items: center; gap: 16px; background: var(--light);
      margin-top: 8px;
    }
    .img-preview-box img {
      width: 72px; height: 72px; border-radius: 8px; object-fit: cover;
      border: 1px solid var(--border);
    }
    .img-preview-box .placeholder {
      width: 72px; height: 72px; border-radius: 8px;
      background: #f0ebe4; display: flex; align-items: center; justify-content: center;
      color: #ccc; font-size: 1.6rem;
    }
    .img-preview-box p { font-size: .78rem; color: #aaa; }

    /* Category pills */
    .cat-pills { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
    .cat-pill input[type=radio] { display: none; }
    .cat-pill label {
      padding: 8px 18px; border-radius: 20px; border: 2px solid var(--border);
      font-size: .82rem; font-weight: 600; cursor: pointer; text-transform: capitalize;
      transition: all .15s; color: #666;
    }
    .cat-pill input:checked + label { border-color: var(--brown); background: var(--brown); color: #fff; }

    /* Alerts */
    .alert { padding: 12px 16px; border-radius: 8px; font-size: .85rem; margin-bottom: 20px; }
    .alert-error { background: #fde8e8; color: #c0392b; }
    .alert ul { padding-left: 18px; }

    /* Buttons */
    .form-actions { display: flex; gap: 12px; margin-top: 28px; }
    .btn-save {
      padding: 12px 28px; background: var(--brown); color: #fff;
      border: none; border-radius: 8px; font-family: inherit;
      font-size: .9rem; font-weight: 700; cursor: pointer;
      transition: background .2s, box-shadow .2s;
    }
    .btn-save:hover { background: #3a2410; box-shadow: 0 4px 14px rgba(81,53,31,.3); }
    .btn-cancel {
      padding: 12px 22px; background: #f0ebe4; color: #555;
      border: none; border-radius: 8px; font-family: inherit;
      font-size: .9rem; font-weight: 600; cursor: pointer; text-decoration: none;
      display: inline-flex; align-items: center;
    }
    .btn-cancel:hover { background: #e5ddd4; }

    @media (max-width: 700px) {
      .form-grid { grid-template-columns: 1fr; }
      .sidebar { width: 60px; }
      .sidebar .brand p, .sidebar nav a span, .sidebar .user-box span { display: none; }
      .main { margin-left: 60px; padding: 20px; }
      .form-card { padding: 22px; }
    }
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="brand"><h2>Vintage</h2><p>Admin Panel</p></div>
  <nav>
    <a href="index.php" class="active"><i class="fa-solid fa-box"></i> <span>Products</span></a>
    <a href="orders.php"><i class="fa-solid fa-receipt"></i> <span>Orders</span></a>
    <a href="users.php"><i class="fa-solid fa-users"></i> <span>Users</span></a>
    <a href="../home.html" target="_blank"><i class="fa-solid fa-store"></i> <span>View Store</span></a>
  </nav>
  <div class="user-box">
    <div class="avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
    <div>
      <span><?= htmlspecialchars($_SESSION['username']) ?></span><br>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
  </div>
</aside>

<main class="main">
  <div class="breadcrumb">
    <a href="index.php">Products</a> / <?= $isEdit ? 'Edit Product' : 'Add Product' ?>
  </div>

  <div class="form-card">
    <h2>
      <i class="fa-solid <?= $isEdit ? 'fa-pen' : 'fa-plus' ?>"></i>
      <?= $isEdit ? 'Edit Product' : 'Add New Product' ?>
    </h2>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="product_form.php">
      <input type="hidden" name="id" value="<?= $isEdit ? $id : 0 ?>">

      <!-- Category -->
      <div class="form-group full" style="margin-bottom:22px">
        <label>Category <span class="required">*</span></label>
        <div class="cat-pills">
          <?php foreach (['jacket','pants','hoodie','shoes'] as $cat): ?>
            <div class="cat-pill">
              <input type="radio" name="category" id="cat_<?= $cat ?>" value="<?= $cat ?>"
                <?= $product['category'] === $cat ? 'checked' : '' ?>>
              <label for="cat_<?= $cat ?>"><?= ucfirst($cat) ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="form-grid">
        <!-- Name -->
        <div class="form-group full">
          <label for="name">Product Name <span class="required">*</span></label>
          <input type="text" id="name" name="name" required maxlength="150"
                 value="<?= htmlspecialchars($product['name']) ?>"
                 placeholder="e.g. Vintage Oversized Leather Jacket">
        </div>

        <!-- Price -->
        <div class="form-group">
          <label for="price">Sale Price ($) <span class="required">*</span></label>
          <input type="number" id="price" name="price" required min="0.01" step="0.01"
                 value="<?= htmlspecialchars($product['price']) ?>" placeholder="59.90">
        </div>

        <!-- Old price -->
        <div class="form-group">
          <label for="old_price">Original Price ($) <span style="color:#aaa;font-weight:400">(optional)</span></label>
          <input type="number" id="old_price" name="old_price" min="0.01" step="0.01"
                 value="<?= htmlspecialchars($product['old_price'] ?? '') ?>" placeholder="79.00">
          <span class="hint">Leave blank if no discount to show.</span>
        </div>

        <!-- Slug -->
        <div class="form-group">
          <label for="slug">Slug</label>
          <input type="text" id="slug" name="slug" maxlength="50"
                 value="<?= htmlspecialchars($product['slug'] ?? '') ?>" placeholder="shop1">
          <span class="hint">Links the product to its HTML page (e.g. shop1).</span>
        </div>

        <!-- Image path -->
        <div class="form-group">
          <label for="image">Image Path</label>
          <input type="text" id="image" name="image" maxlength="255"
                 value="<?= htmlspecialchars($product['image'] ?? '') ?>"
                 placeholder="image/1.1.avif" oninput="updatePreview(this.value)">
          <span class="hint">Relative path from the project root.</span>
        </div>

        <!-- Preview -->
        <div class="form-group full">
          <div class="img-preview-box" id="previewBox">
            <?php if (!empty($product['image'])): ?>
              <img id="previewImg" src="../<?= htmlspecialchars($product['image']) ?>" alt="preview"
                   onerror="this.style.display='none';document.getElementById('previewPlaceholder').style.display='flex'">
              <div id="previewPlaceholder" class="placeholder" style="display:none"><i class="fa-solid fa-image"></i></div>
            <?php else: ?>
              <div id="previewPlaceholder" class="placeholder"><i class="fa-solid fa-image"></i></div>
              <img id="previewImg" src="" alt="preview" style="display:none">
            <?php endif; ?>
            <p>Image preview — enter a path above.</p>
          </div>
        </div>

        <!-- Description -->
        <div class="form-group full">
          <label for="description">Description</label>
          <textarea id="description" name="description" placeholder="Describe the product…"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save">
          <i class="fa-solid <?= $isEdit ? 'fa-floppy-disk' : 'fa-plus' ?>"></i>
          <?= $isEdit ? 'Save Changes' : 'Add Product' ?>
        </button>
        <a href="index.php" class="btn-cancel">Cancel</a>
      </div>
    </form>
  </div>
</main>

<script>
function updatePreview(path) {
  const img = document.getElementById('previewImg');
  const placeholder = document.getElementById('previewPlaceholder');
  if (path.trim() === '') {
    img.style.display = 'none';
    placeholder.style.display = 'flex';
    return;
  }
  img.src = '../' + path.trim();
  img.style.display = 'block';
  placeholder.style.display = 'none';
  img.onerror = function() {
    this.style.display = 'none';
    placeholder.style.display = 'flex';
  };
}
</script>
</body>
</html>

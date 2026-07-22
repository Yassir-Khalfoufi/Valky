<?php
// admin/index.php  —  main dashboard
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$pdo = getPDO();

// ── Stats ──────────────────────────────────────────────────────────────────
$stats = $pdo->query("
    SELECT
        (SELECT COUNT(*) FROM products) AS total_products,
        (SELECT COUNT(*) FROM users)    AS total_users,
        (SELECT COUNT(*) FROM orders)   AS total_orders,
        (SELECT COALESCE(SUM(total),0) FROM orders WHERE status='paid') AS revenue
")->fetch();

// ── Filter / Search ────────────────────────────────────────────────────────
$category = $_GET['cat']    ?? '';
$search   = trim($_GET['q'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 10;
$offset   = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($category !== '') { $where[] = "category = ?"; $params[] = $category; }
if ($search    !== '') { $where[] = "(name LIKE ? OR description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total_rows = $pdo->prepare("SELECT COUNT(*) FROM products $whereSQL");
$total_rows->execute($params);
$total_pages = max(1, (int)ceil($total_rows->fetchColumn() / $perPage));

$stmt = $pdo->prepare("SELECT * FROM products $whereSQL ORDER BY category, id LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll();

// ── Flash messages ─────────────────────────────────────────────────────────
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – Products</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --brown:  #51351f;
      --brown2: #8c5d38;
      --light:  #fdf9f5;
      --border: #e8e0d6;
      --red:    #e74c3c;
      --green:  #27ae60;
      --blue:   #2980b9;
    }
    body { font-family: 'Montserrat', sans-serif; background: #f5f0ea; color: #2d2d2d; }

    /* ── Sidebar ── */
    .sidebar {
      position: fixed; top: 0; left: 0; width: 220px; height: 100vh;
      background: var(--brown); color: #fff; display: flex; flex-direction: column;
      z-index: 100;
    }
    .sidebar .brand { padding: 28px 20px 20px; border-bottom: 1px solid rgba(255,255,255,.15); }
    .sidebar .brand h2 { font-size: .95rem; letter-spacing: 2px; text-transform: uppercase; }
    .sidebar .brand p  { font-size: .7rem; opacity: .6; margin-top: 3px; }
    .sidebar nav { flex: 1; padding: 16px 0; }
    .sidebar nav a {
      display: flex; align-items: center; gap: 10px;
      padding: 11px 20px; color: rgba(255,255,255,.8);
      text-decoration: none; font-size: .85rem; font-weight: 500;
      transition: background .15s, color .15s;
    }
    .sidebar nav a:hover, .sidebar nav a.active {
      background: rgba(255,255,255,.12); color: #fff;
    }
    .sidebar nav a i { width: 18px; text-align: center; }
    .sidebar .user-box {
      padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.15);
      font-size: .78rem; display: flex; align-items: center; gap: 10px;
    }
    .sidebar .user-box .avatar {
      width: 32px; height: 32px; border-radius: 50%;
      background: var(--brown2); display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }
    .sidebar .user-box a { color: rgba(255,255,255,.6); text-decoration: none; font-size: .75rem; }
    .sidebar .user-box a:hover { color: #fff; }

    /* ── Main ── */
    .main { margin-left: 220px; padding: 32px; min-height: 100vh; }

    /* ── Topbar ── */
    .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
    .topbar h1 { font-size: 1.4rem; color: var(--brown); }
    .btn-add {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 10px 20px; background: var(--brown); color: #fff;
      border: none; border-radius: 8px; font-size: .85rem; font-weight: 600;
      font-family: inherit; cursor: pointer; text-decoration: none;
      transition: background .2s, box-shadow .2s;
    }
    .btn-add:hover { background: #3a2410; box-shadow: 0 4px 14px rgba(81,53,31,.3); }

    /* ── Stats ── */
    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
    .stat-card {
      background: #fff; border-radius: 12px; padding: 20px 22px;
      box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }
    .stat-card .label { font-size: .72rem; color: #999; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .stat-card .value { font-size: 1.9rem; font-weight: 700; color: var(--brown); margin-top: 6px; }
    .stat-card .icon  { float: right; font-size: 1.8rem; opacity: .15; margin-top: -40px; color: var(--brown); }

    /* ── Table card ── */
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.06); overflow: hidden; }

    /* ── Filters ── */
    .filters {
      display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
      padding: 16px 20px; border-bottom: 1px solid var(--border);
    }
    .filters form { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1; }
    .filters input[type=text] {
      padding: 8px 12px; border: 1.5px solid var(--border); border-radius: 7px;
      font-size: .85rem; font-family: inherit; flex: 1; min-width: 180px;
    }
    .filters input:focus { outline: none; border-color: var(--brown); }
    .filters select {
      padding: 8px 12px; border: 1.5px solid var(--border); border-radius: 7px;
      font-size: .85rem; font-family: inherit; background: #fff;
    }
    .filters select:focus { outline: none; border-color: var(--brown); }
    .btn-search {
      padding: 8px 16px; background: var(--brown); color: #fff;
      border: none; border-radius: 7px; font-family: inherit; font-size: .85rem;
      cursor: pointer; font-weight: 600;
    }
    .btn-reset { padding: 8px 12px; background: #f0ebe4; border: none; border-radius: 7px; font-family: inherit; font-size: .82rem; cursor: pointer; color: #666; }

    /* ── Table ── */
    table { width: 100%; border-collapse: collapse; }
    thead th {
      background: #faf7f3; padding: 12px 16px;
      text-align: left; font-size: .75rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .8px; color: #888;
      border-bottom: 1px solid var(--border);
    }
    tbody tr { border-bottom: 1px solid #f5f0ea; transition: background .1s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #fdf9f5; }
    tbody td { padding: 12px 16px; font-size: .88rem; vertical-align: middle; }
    .product-thumb {
      width: 48px; height: 48px; border-radius: 8px;
      object-fit: cover; display: block;
    }
    .product-thumb-placeholder {
      width: 48px; height: 48px; border-radius: 8px;
      background: #f0ebe4; display: flex; align-items: center; justify-content: center;
      color: #ccc; font-size: 1.2rem;
    }
    .badge {
      display: inline-block; padding: 3px 10px; border-radius: 20px;
      font-size: .72rem; font-weight: 600; text-transform: capitalize;
    }
    .badge-jacket  { background: #f3e9df; color: #7a4a1e; }
    .badge-pants   { background: #e9f3df; color: #3a6e1e; }
    .badge-hoodie  { background: #dff0f3; color: #1e5f6e; }
    .badge-shoes   { background: #f3dff0; color: #6e1e68; }
    .actions { display: flex; gap: 8px; }
    .btn-edit, .btn-delete {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 6px 12px; border-radius: 6px;
      font-size: .78rem; font-weight: 600; font-family: inherit;
      border: none; cursor: pointer; text-decoration: none; transition: opacity .15s;
    }
    .btn-edit   { background: #e8f3fe; color: var(--blue); }
    .btn-delete { background: #fde8e8; color: var(--red); }
    .btn-edit:hover, .btn-delete:hover { opacity: .75; }

    /* ── Pagination ── */
    .pagination { display: flex; align-items: center; gap: 6px; padding: 16px 20px; justify-content: flex-end; }
    .pagination a, .pagination span {
      padding: 6px 12px; border-radius: 6px; font-size: .82rem; font-weight: 600;
      text-decoration: none; color: var(--brown);
    }
    .pagination a:hover  { background: #f0ebe4; }
    .pagination .current { background: var(--brown); color: #fff; }
    .pagination .disabled { color: #ccc; cursor: default; }

    /* ── Flash ── */
    .flash {
      padding: 12px 18px; border-radius: 9px; margin-bottom: 20px;
      font-size: .88rem; font-weight: 500; display: flex; align-items: center; gap: 10px;
    }
    .flash-success { background: #e8f8ee; color: var(--green); }
    .flash-error   { background: #fde8e8; color: var(--red); }

    /* ── Responsive ── */
    @media (max-width: 900px) {
      .sidebar { width: 60px; }
      .sidebar .brand p, .sidebar nav a span, .sidebar .user-box span { display: none; }
      .main { margin-left: 60px; padding: 20px; }
      .stats { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
  <div class="brand">
    <h2>Vintage</h2>
    <p>Admin Panel</p>
  </div>
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

<!-- Main -->
<main class="main">
  <div class="topbar">
    <h1><i class="fa-solid fa-box" style="opacity:.5;margin-right:8px"></i>Products</h1>
    <a href="product_form.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add Product</a>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="stat-card">
      <div class="label">Products</div>
      <div class="value"><?= $stats['total_products'] ?></div>
      <i class="fa-solid fa-box icon"></i>
    </div>
    <div class="stat-card">
      <div class="label">Users</div>
      <div class="value"><?= $stats['total_users'] ?></div>
      <i class="fa-solid fa-users icon"></i>
    </div>
    <div class="stat-card">
      <div class="label">Orders</div>
      <div class="value"><?= $stats['total_orders'] ?></div>
      <i class="fa-solid fa-receipt icon"></i>
    </div>
    <div class="stat-card">
      <div class="label">Revenue</div>
      <div class="value">$<?= number_format($stats['revenue'], 0) ?></div>
      <i class="fa-solid fa-dollar-sign icon"></i>
    </div>
  </div>

  <?php if ($flash !== ''): ?>
    <?php $isErr = str_starts_with($flash, 'ERR:'); ?>
    <div class="flash <?= $isErr ? 'flash-error' : 'flash-success' ?>">
      <i class="fa-solid <?= $isErr ? 'fa-circle-xmark' : 'fa-circle-check' ?>"></i>
      <?= htmlspecialchars($isErr ? substr($flash, 4) : $flash) ?>
    </div>
  <?php endif; ?>

  <!-- Table card -->
  <div class="card">
    <!-- Filters -->
    <div class="filters">
      <form method="GET" action="index.php">
        <input type="text" name="q" placeholder="Search products…" value="<?= htmlspecialchars($search) ?>">
        <select name="cat">
          <option value="">All categories</option>
          <?php foreach (['jacket','pants','hoodie','shoes'] as $cat): ?>
            <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        <?php if ($search !== '' || $category !== ''): ?>
          <a href="index.php" class="btn-reset">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Table -->
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Image</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Old Price</th>
          <th>Slug</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
          <tr><td colspan="8" style="text-align:center;padding:32px;color:#aaa">No products found.</td></tr>
        <?php else: ?>
          <?php foreach ($products as $p): ?>
            <tr>
              <td style="color:#aaa;font-size:.8rem"><?= $p['id'] ?></td>
              <td>
                <?php if (!empty($p['image'])): ?>
                  <img class="product-thumb" src="../<?= htmlspecialchars($p['image']) ?>" alt=""
                       onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                  <div class="product-thumb-placeholder" style="display:none"><i class="fa-solid fa-image"></i></div>
                <?php else: ?>
                  <div class="product-thumb-placeholder"><i class="fa-solid fa-image"></i></div>
                <?php endif; ?>
              </td>
              <td style="font-weight:600;max-width:200px"><?= htmlspecialchars($p['name']) ?></td>
              <td><span class="badge badge-<?= $p['category'] ?>"><?= ucfirst($p['category']) ?></span></td>
              <td><strong>$<?= number_format($p['price'], 2) ?></strong></td>
              <td><?= $p['old_price'] ? '<del style="color:#aaa">$' . number_format($p['old_price'], 2) . '</del>' : '—' ?></td>
              <td style="color:#aaa;font-size:.82rem"><?= htmlspecialchars($p['slug'] ?? '') ?></td>
              <td>
                <div class="actions">
                  <a class="btn-edit" href="product_form.php?id=<?= $p['id'] ?>">
                    <i class="fa-solid fa-pen"></i> Edit
                  </a>
                  <button class="btn-delete" onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php
          $base = 'index.php?' . http_build_query(array_filter(['q' => $search, 'cat' => $category]));
          $base .= $base !== 'index.php?' ? '&' : '?';
        ?>
        <?php if ($page > 1): ?>
          <a href="<?= $base ?>page=<?= $page - 1 ?>"><i class="fa-solid fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <?php if ($i === $page): ?>
            <span class="current"><?= $i ?></span>
          <?php else: ?>
            <a href="<?= $base ?>page=<?= $i ?>"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
          <a href="<?= $base ?>page=<?= $page + 1 ?>"><i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<!-- Delete confirmation (hidden form) -->
<form id="deleteForm" method="POST" action="product_delete.php" style="display:none">
  <input type="hidden" name="id" id="deleteId">
</form>

<script>
function confirmDelete(id, name) {
  if (confirm('Delete "' + name + '"?\n\nThis cannot be undone.')) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteForm').submit();
  }
}
</script>
</body>
</html>

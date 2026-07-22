<?php
// admin/orders.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$pdo = getPDO();

// Status filter
$status = $_GET['status'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$per    = 15;
$offset = ($page - 1) * $per;

$where  = $status ? "WHERE o.status = ?" : '';
$params = $status ? [$status] : [];

$total = $pdo->prepare("SELECT COUNT(*) FROM orders o $where");
$total->execute($params);
$total_pages = max(1, (int)ceil($total->fetchColumn() / $per));

$stmt = $pdo->prepare(
    "SELECT o.*, u.username, p.name AS product_name, p.category
     FROM orders o
     JOIN users u ON u.id = o.user_id
     JOIN products p ON p.id = o.product_id
     $where
     ORDER BY o.ordered_at DESC
     LIMIT $per OFFSET $offset"
);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Handle status update
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $oid     = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'] ?? '';
    $allowed = ['pending','paid','shipped','cancelled'];
    if ($oid > 0 && in_array($new_status, $allowed)) {
        $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$new_status, $oid]);
        $_SESSION['flash'] = "Order #$oid status updated to $new_status.";
    }
    header('Location: orders.php' . ($status ? '?status=' . $status : ''));
    exit;
}

$statuses = ['pending','paid','shipped','cancelled'];
$statusColors = [
    'pending'   => ['#fff3cd','#856404'],
    'paid'      => ['#e8f8ee','#27ae60'],
    'shipped'   => ['#dff0f3','#1e5f6e'],
    'cancelled' => ['#fde8e8','#c0392b'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders – Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root { --brown: #51351f; --brown2: #8c5d38; --border: #e8e0d6; }
    body { font-family: 'Montserrat', sans-serif; background: #f5f0ea; color: #2d2d2d; }
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
      transition: background .15s;
    }
    .sidebar nav a:hover, .sidebar nav a.active { background: rgba(255,255,255,.12); color: #fff; }
    .sidebar nav a i { width: 18px; text-align: center; }
    .sidebar .user-box {
      padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.15);
      font-size: .78rem; display: flex; align-items: center; gap: 10px;
    }
    .sidebar .user-box .avatar {
      width: 32px; height: 32px; border-radius: 50%; background: var(--brown2);
      display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }
    .sidebar .user-box a { color: rgba(255,255,255,.6); text-decoration: none; font-size: .75rem; }
    .main { margin-left: 220px; padding: 32px; }
    .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .topbar h1 { font-size: 1.4rem; color: var(--brown); }
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .filter-tabs a {
      padding: 7px 18px; border-radius: 20px; font-size: .8rem; font-weight: 600;
      text-decoration: none; border: 2px solid var(--border); color: #666;
      transition: all .15s;
    }
    .filter-tabs a:hover, .filter-tabs a.active { border-color: var(--brown); background: var(--brown); color: #fff; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      background: #faf7f3; padding: 12px 16px; text-align: left;
      font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px;
      color: #888; border-bottom: 1px solid var(--border);
    }
    tbody tr { border-bottom: 1px solid #f5f0ea; transition: background .1s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #fdf9f5; }
    tbody td { padding: 11px 16px; font-size: .86rem; vertical-align: middle; }
    .badge {
      display: inline-block; padding: 3px 10px; border-radius: 20px;
      font-size: .72rem; font-weight: 600; text-transform: capitalize;
    }
    .status-select {
      padding: 5px 8px; border: 1.5px solid var(--border); border-radius: 6px;
      font-size: .78rem; font-family: inherit; background: #fdf9f5; cursor: pointer;
    }
    .btn-update {
      padding: 5px 12px; background: var(--brown); color: #fff;
      border: none; border-radius: 6px; font-size: .75rem; font-family: inherit;
      font-weight: 600; cursor: pointer;
    }
    .flash { padding: 12px 18px; border-radius: 9px; margin-bottom: 20px; font-size: .88rem; font-weight: 500; }
    .flash-success { background: #e8f8ee; color: #27ae60; }
    .pagination { display: flex; gap: 6px; padding: 16px 20px; justify-content: flex-end; }
    .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: .82rem; font-weight: 600; text-decoration: none; color: var(--brown); }
    .pagination a:hover { background: #f0ebe4; }
    .pagination .current { background: var(--brown); color: #fff; }
    @media(max-width:900px) { .sidebar{width:60px} .sidebar .brand p,.sidebar nav a span,.sidebar .user-box span{display:none} .main{margin-left:60px;padding:20px} }
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="brand"><h2>Vintage</h2><p>Admin Panel</p></div>
  <nav>
    <a href="index.php"><i class="fa-solid fa-box"></i> <span>Products</span></a>
    <a href="orders.php" class="active"><i class="fa-solid fa-receipt"></i> <span>Orders</span></a>
    <a href="users.php"><i class="fa-solid fa-users"></i> <span>Users</span></a>
    <a href="../home.html" target="_blank"><i class="fa-solid fa-store"></i> <span>View Store</span></a>
  </nav>
  <div class="user-box">
    <div class="avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
    <div><span><?= htmlspecialchars($_SESSION['username']) ?></span><br>
    <a href="logout.php">Logout</a></div>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <h1><i class="fa-solid fa-receipt" style="opacity:.5;margin-right:8px"></i>Orders</h1>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <div class="filter-tabs">
    <a href="orders.php" <?= $status === '' ? 'class="active"' : '' ?>>All</a>
    <?php foreach ($statuses as $s): ?>
      <a href="orders.php?status=<?= $s ?>" <?= $status === $s ? 'class="active"' : '' ?>><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Product</th>
          <th>Size</th>
          <th>Qty</th>
          <th>Total</th>
          <th>Card</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($orders)): ?>
          <tr><td colspan="9" style="text-align:center;padding:32px;color:#aaa">No orders found.</td></tr>
        <?php else: ?>
          <?php foreach ($orders as $o):
            [$bg, $fg] = $statusColors[$o['status']] ?? ['#eee','#555'];
          ?>
            <tr>
              <td style="color:#aaa;font-size:.78rem"><?= $o['id'] ?></td>
              <td style="font-weight:600"><?= htmlspecialchars($o['username']) ?></td>
              <td>
                <span style="font-weight:500"><?= htmlspecialchars(mb_strimwidth($o['product_name'], 0, 35, '…')) ?></span><br>
                <span style="font-size:.72rem;color:#aaa"><?= ucfirst($o['category']) ?></span>
              </td>
              <td><?= htmlspecialchars($o['size']) ?></td>
              <td><?= $o['quantity'] ?></td>
              <td><strong>$<?= number_format($o['total'], 2) ?></strong></td>
              <td style="font-size:.78rem;color:#aaa">*<?= htmlspecialchars($o['card_last4']) ?></td>
              <td style="font-size:.78rem;color:#888"><?= date('d M Y', strtotime($o['ordered_at'])) ?></td>
              <td>
                <form method="POST" action="orders.php<?= $status ? '?status='.$status : '' ?>" style="display:flex;gap:6px;align-items:center">
                  <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                  <input type="hidden" name="update_status" value="1">
                  <select name="new_status" class="status-select">
                    <?php foreach ($statuses as $s): ?>
                      <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn-update">Save</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <?php if ($i === $page): ?>
            <span class="current"><?= $i ?></span>
          <?php else: ?>
            <a href="orders.php?<?= http_build_query(['status' => $status, 'page' => $i]) ?>"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</main>
</body>
</html>

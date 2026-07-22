<?php
// admin/users.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$pdo   = getPDO();
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);

$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$per    = 15; $offset = ($page - 1) * $per;

$where  = $search ? "WHERE username LIKE ? OR email LIKE ?" : '';
$params = $search ? ["%$search%", "%$search%"] : [];

$total = $pdo->prepare("SELECT COUNT(*) FROM users $where");
$total->execute($params);
$total_pages = max(1, (int)ceil($total->fetchColumn() / $per));

$stmt = $pdo->prepare(
    "SELECT u.*, (SELECT COUNT(*) FROM orders o WHERE o.user_id=u.id) AS order_count
     FROM users u $where ORDER BY u.created_at DESC LIMIT $per OFFSET $offset"
);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users – Admin</title>
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
      color: rgba(255,255,255,.8); text-decoration: none; font-size: .85rem; font-weight: 500; transition: background .15s;
    }
    .sidebar nav a:hover, .sidebar nav a.active { background: rgba(255,255,255,.12); color: #fff; }
    .sidebar nav a i { width: 18px; text-align: center; }
    .sidebar .user-box {
      padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.15);
      font-size: .78rem; display: flex; align-items: center; gap: 10px;
    }
    .sidebar .user-box .avatar {
      width: 32px; height: 32px; border-radius: 50%; background: var(--brown2);
      display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;
    }
    .sidebar .user-box a { color: rgba(255,255,255,.6); text-decoration: none; font-size: .75rem; }
    .main { margin-left: 220px; padding: 32px; }
    .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .topbar h1 { font-size: 1.4rem; color: var(--brown); }
    .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
    .search-bar input {
      flex: 1; max-width: 340px; padding: 10px 14px; border: 1.5px solid var(--border);
      border-radius: 8px; font-size: .88rem; font-family: inherit; background: #fff;
    }
    .search-bar input:focus { outline: none; border-color: var(--brown); }
    .search-bar button {
      padding: 10px 18px; background: var(--brown); color: #fff;
      border: none; border-radius: 8px; font-family: inherit; font-size: .85rem;
      font-weight: 600; cursor: pointer;
    }
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
    tbody td { padding: 12px 16px; font-size: .86rem; vertical-align: middle; }
    .avatar-cell {
      width: 36px; height: 36px; border-radius: 50%; background: var(--brown2);
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-weight: 700; font-size: .9rem;
    }
    .badge-admin { display:inline-block; padding:2px 9px; border-radius:20px; font-size:.7rem; font-weight:700; background:#51351f; color:#fff; }
    .badge-user  { display:inline-block; padding:2px 9px; border-radius:20px; font-size:.7rem; font-weight:600; background:#f0ebe4; color:#888; }
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
    <a href="orders.php"><i class="fa-solid fa-receipt"></i> <span>Orders</span></a>
    <a href="users.php" class="active"><i class="fa-solid fa-users"></i> <span>Users</span></a>
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
    <h1><i class="fa-solid fa-users" style="opacity:.5;margin-right:8px"></i>Users</h1>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-success"><?= htmlspecialchars($flash) ?></div>
  <?php endif; ?>

  <form class="search-bar" method="GET" action="users.php">
    <input type="text" name="q" placeholder="Search by username or email…" value="<?= htmlspecialchars($search) ?>">
    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    <?php if ($search): ?><a href="users.php" style="padding:10px 14px;background:#f0ebe4;border-radius:8px;font-size:.85rem;text-decoration:none;color:#666">Clear</a><?php endif; ?>
  </form>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th></th>
          <th>#</th>
          <th>Username</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Orders</th>
          <th>Joined</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr><td colspan="8" style="text-align:center;padding:32px;color:#aaa">No users found.</td></tr>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><div class="avatar-cell"><?= strtoupper(substr($u['username'], 0, 1)) ?></div></td>
              <td style="color:#aaa;font-size:.78rem"><?= $u['id'] ?></td>
              <td style="font-weight:600"><?= htmlspecialchars($u['username']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td style="color:#888"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
              <td><span class="<?= $u['is_admin'] ? 'badge-admin' : 'badge-user' ?>"><?= $u['is_admin'] ? 'Admin' : 'User' ?></span></td>
              <td style="text-align:center"><?= $u['order_count'] ?></td>
              <td style="color:#aaa;font-size:.78rem"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if ($total_pages > 1): ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <?php if ($i === $page): ?><span class="current"><?= $i ?></span>
          <?php else: ?><a href="users.php?<?= http_build_query(['q'=>$search,'page'=>$i]) ?>"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</main>
</body>
</html>

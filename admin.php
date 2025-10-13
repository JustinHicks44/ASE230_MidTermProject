<?php
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/storage.php';
auth_init_default_admin();
auth_require_role(['admin']);

$type = $_GET['type'] ?? 'users'; // users or items
$action = $_GET['action'] ?? 'index';

// Simple router
if ($type !== 'users' && $type !== 'items') {
  $type = 'users';
}

if ($action === 'delete' && !empty($_GET['id'])) {
    storage_delete($type, $_GET['id']);
    header('Location: admin.php?type=' . $type);
    exit;
}

$items = storage_read($type);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - Manage <?=htmlspecialchars($type)?></title>
  <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
  <div class="container my-5">
    <h1>Admin - <?=htmlspecialchars($type)?></h1>
    <p><a href="admin.php?type=users">Users</a> | <a href="admin.php?type=items">Items</a> | <a href="logout.php">Sign out</a></p>
    <p><a class="btn btn-primary" href="admin_edit.php?type=<?=urlencode($type)?>">Create new</a></p>
    <table class="table table-striped">
      <thead><tr>
        <th>ID</th>
        <th>Summary</th>
        <th>Actions</th>
      </tr></thead>
      <tbody>
        <?php foreach($items as $it): ?>
          <tr>
            <td><?=htmlspecialchars($it['id'] ?? '')?></td>
            <td>
              <?php if ($type === 'users'): ?>
                <?=htmlspecialchars($it['username'] ?? $it['email'] ?? '')?>
              <?php else: ?>
                <?=htmlspecialchars($it['title'] ?? $it['name'] ?? 'Item')?>
              <?php endif; ?>
            </td>
            <td>
              <a class="btn btn-sm btn-secondary" href="admin_edit.php?type=<?=urlencode($type)?>&id=<?=urlencode($it['id'] ?? '')?>">Edit</a>
              <a class="btn btn-sm btn-danger" href="admin.php?type=<?=urlencode($type)?>&action=delete&id=<?=urlencode($it['id'] ?? '')?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>

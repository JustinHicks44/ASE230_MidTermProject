<?php
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/storage.php';
auth_init_default_admin();
auth_require_role(['admin']);

$type = $_GET['type'] ?? 'users';
if ($type !== 'users' && $type !== 'items') $type = 'users';

$id = $_GET['id'] ?? '';
$item = null;
if ($id) {
    $item = storage_find($type, function($i) use ($id) { return isset($i['id']) && $i['id'] === $id; });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;
    if ($type === 'users') {
        // when setting password, hash it
        if (!empty($post['password'])) {
            $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
        } else {
            unset($post['password']);
        }
    }
    storage_upsert($type, $post);
    header('Location: admin.php?type=' . $type);
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit <?=htmlspecialchars($type)?></title>
  <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
  <div class="container my-5">
    <h1><?= $id ? 'Edit' : 'Create' ?> <?=htmlspecialchars($type)?></h1>
    <form method="post">
      <?php if ($type === 'users') { ?>
        <input type="hidden" name="id" value="<?=htmlspecialchars($item['id'] ?? '')?>" />
        <div class="mb-3"><label for="username" class="form-label">Username</label><input id="username" name="username" class="form-control" value="<?=htmlspecialchars($item['username'] ?? '')?>" required /></div>
        <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" class="form-control" value="<?=htmlspecialchars($item['email'] ?? '')?>" required /></div>
        <div class="mb-3"><label for="password" class="form-label">Password (leave blank to keep)</label><input id="password" name="password" type="password" class="form-control" /></div>
        <div class="mb-3"><label for="role" class="form-label">Role</label><select id="role" name="role" class="form-select"><option value="user" <?=isset($item['role']) && $item['role']==='user'?'selected':''?>>User</option><option value="admin" <?=isset($item['role']) && $item['role']==='admin'?'selected':''?>>Admin</option></select></div>
      <?php } else { ?>
        <input type="hidden" name="id" value="<?=htmlspecialchars($item['id'] ?? '')?>" />
        <div class="mb-3"><label for="title" class="form-label">Title</label><input id="title" name="title" class="form-control" value="<?=htmlspecialchars($item['title'] ?? '')?>" required /></div>
        <div class="mb-3"><label for="description" class="form-label">Description</label><textarea id="description" name="description" class="form-control"><?=htmlspecialchars($item['description'] ?? '')?></textarea></div>
      <?php } ?>
      <div class="mb-3"><button class="btn btn-primary">Save</button> <a class="btn btn-link" href="admin.php?type=<?=urlencode($type)?>">Cancel</a></div>
    </form>
  </div>
</body>
</html>

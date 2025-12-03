<?php
require_once __DIR__ . '/lib/auth.php';
auth_init_default_admin();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $res = auth_signin($username, $password);
    if ($res['ok']) {
        header('Location: index.php');
        exit;
    } else {
        $error = $res['error'];
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign in</title>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
    <div class="container my-5">
        <h2>Sign in</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" class="form-control" required />
            </div>
            <div class="mb-3">
                <button class="btn btn-primary">Sign in</button>
                <a class="btn btn-link" href="accountsignup.php">Create account</a>
            </div>
        </form>
    </div>
</body>
</html>

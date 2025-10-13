<?php
require_once __DIR__ . '/lib/auth.php';
auth_init_default_admin();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $res = auth_signup($username, $email, $password);
    if ($res['ok']) {
        header('Location: index.php');
        exit;
    } else {
        $error = $res['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Sign Up</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container px-5">
                <a class="navbar-brand" href="index.php">Start Bootstrap</a>
            </div>
        </nav>
        <div class="container px-4 px-lg-5">
            <div class="row justify-content-center my-5">
                <div class="col-md-6">
                    <h2>Create an account</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input id="username" name="username" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" name="password" type="password" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary">Sign up</button>
                            <a class="btn btn-link" href="login.php">Already have an account?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>

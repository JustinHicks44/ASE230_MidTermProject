<?php
require_once __DIR__ . '/lib/auth.php';
auth_init_default_admin();
$user = auth_current_user();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>MyTicket - Home</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="index.php">MyTicket</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>                        
						<?php if ($user): ?>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Sign out (<?=htmlspecialchars($user['username'])?>)</a></li>
                            <?php if (isset($user['role']) && $user['role'] === 'admin'): ?>
                                <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="accountsignup.php">Sign up</a></li>
                            <li class="nav-item"><a class="nav-link" href="login.php">Sign in</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page Content-->
        <div class="container px-4 px-lg-5">
            <!-- Heading Row-->
            <div class="row gx-4 gx-lg-5 align-items-center my-5">
                <div class="col-lg-7"><img class="img-fluid rounded mb-4 mb-lg-0" src="ball-racket-are-grass-with-balls-ball_979520-151391.jpg" alt="..." /></div>
                <div class="col-lg-5">
                    <h1 class="font-weight-light">Welcome to MyTicket</h1>
                    <p>We are MyTicket, a frontrunner in the ticket sale/event administration business! Whether you are a concert goer looking for your next show or an event runner hoping to sell as many tickets as you can, you're in the right place!</p>
                    <a class="btn btn-primary" href="accountsignup.php">Sign Up!</a>
                </div>
            </div>
            <!-- Call to Action-->
            <div class="card text-white bg-secondary my-5 py-4 text-center">
                <div class="card-body"><p class="text-white m-0">Your one-stop shop for all things events: sports, concerts, fairs, public events, whatever you can think of!</p></div>
            </div>
            <!-- Content Row-->
            <div class="row gx-4 gx-lg-5">
                <div class="col-md-4 mb-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Sign Up</h2>
                            <p class="card-text">Advertise your event or find your next concert!</p>
                        </div>
                        <div class="card-footer"><a class="btn btn-primary btn-sm" href="accountsignup.php">Sign up here!</a></div>
                    </div>
                </div>
                <div class="col-md-4 mb-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Sign In</h2>
                            <p class="card-text">Sign in to your account to view your tickets and manage your account.</p>
                        </div>
                        <div class="card-footer"><a class="btn btn-primary btn-sm" href="login.php">Sign in!</a></div>
                    </div>
                </div>
                <div class="col-md-4 mb-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="card-title">Contact Us</h2>
                            <p class="card-text">Contact us with any questions or if you need any help!</p>
                        </div>
                        <div class="card-footer"><a class="btn btn-primary btn-sm" href="contact.php">Contact us!</a></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; MyTicket 2025</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>

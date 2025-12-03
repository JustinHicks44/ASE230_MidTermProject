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
        <title>MyTicket - About</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
	</head>
	
	<body>
		<header>
			<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-5">
                <a class="navbar-brand" href="index.php">MyTicket</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="about.php">About</a></li>
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
		</header>
		
		<main>
			<h2>About Us</h2>
			<p>At MyTicket, we believe buying and selling event tickets should be simple, transparent, and enjoyable—for fans and venues alike. Born from a passion for live entertainment, we set out to create a smarter, fairer alternative to traditional ticketing platforms.</p>
			<br>
			<p>Whether you’re looking for the best seats at your favorite band’s concert, cheering courtside at a big game, or hosting an event of your own, MyTicket makes the process effortless. Our platform connects fans directly to verified venues and event organizers, eliminating hidden fees and frustrating markups.</p>
			<br>
			<p>We’re more than just a ticket marketplace—we’re a community built around experiences. From user-friendly tools for fans to powerful analytics for venues and promoters, MyTicket empowers everyone to get more out of live events.</p>
			<br>
			<p><strong>Our Mission:</strong> To redefine ticketing by making it more accessible, transparent, and rewarding for fans, artists, and venues everywhere.
			<br>
			<p><strong>Our Promise:</strong> Every ticket. Every event. A better experience.</p>
		</main>
		
		<footer class="py-5 bg-dark">
            <div class="container px-4 px-lg-5"><p class="m-0 text-center text-white">Copyright &copy; MyTicket 2025</p></div>
        </footer>
		<!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
	</body>
</html>

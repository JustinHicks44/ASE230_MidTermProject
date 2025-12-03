<?php
require_once __DIR__ . '/lib/auth.php';
auth_signout();
header('Location: index.php');
exit;

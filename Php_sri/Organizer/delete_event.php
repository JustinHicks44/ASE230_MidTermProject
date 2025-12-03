<?php
require "../lib/db.php";

$organizerID = 2;

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid ID");
}

$eventID = $_GET['id'];

// Delete
$sql = "
    DELETE FROM Events
    WHERE EventID = ? AND OrganizerID = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$eventID, $organizerID]);

header("Location: dashboard.php?deleted=1");
exit;

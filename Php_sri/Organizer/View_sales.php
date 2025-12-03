<?php
require "../lib/db.php";

$organizerID = 2;

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid ID");
}

$eventID = $_GET['id'];

// Verify event belongs to organizer
$check = $pdo->prepare("
    SELECT Title FROM Events
    WHERE EventID = ? AND OrganizerID = ?
");
$check->execute([$eventID, $organizerID]);
$event = $check->fetch(PDO::FETCH_ASSOC);

if (!$event) die("Unauthorized");

// Fetch sales
$sql = "
    SELECT 
        Tickets.TicketID,
        Tickets.UniqueBarcode,
        Tickets.PurchasePrice,
        Orders.OrderDate,
        Users.Username AS CustomerName
    FROM Tickets
    JOIN TicketTypes ON Tickets.TicketTypeID = TicketTypes.TicketTypeID
    JOIN Orders ON Tickets.OrderID = Orders.OrderID
    JOIN Users ON Orders.CustomerID = Users.UserID
    WHERE TicketTypes.EventID = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$eventID]);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales - <?= htmlspecialchars($event['Title']) ?></title>

    <link rel="stylesheet" href="/Css_sri/global.css">
    <link rel="stylesheet" href="/Css_sri/organisers.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

<div class="container mt-5">

    <h1>Sales for: <?= htmlspecialchars($event['Title']) ?></h1>
    <hr>

    <?php if (empty($sales)): ?>
        <div class="alert alert-info">No ticket sales yet.</div>
    <?php else: ?>

        <table class="table table-striped table-bordered shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Barcode</th>
                    <th>Customer</th>
                    <th>Purchase Price</th>
                    <th>Order Date</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($sales as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['UniqueBarcode']) ?></td>
                    <td><?= htmlspecialchars($s['CustomerName']) ?></td>
                    <td>$<?= number_format($s['PurchasePrice'], 2) ?></td>
                    <td><?= date("M d, Y h:i A", strtotime($s['OrderDate'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>

</div>

</body>
</html>

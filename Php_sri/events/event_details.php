<?php
require "../lib/db.php"; // Database connection

// ---------------------------------------------------------
// VALIDATE EVENT ID
// ---------------------------------------------------------
$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    http_response_code(404);
    $event = null;
} else {

    try {
        // ---------------------------------------------------------
        // FETCH EVENT + CATEGORY + LOCATION + ORGANIZER
        // ---------------------------------------------------------
        $sql = "
        SELECT 
            Events.EventID,
            Events.Title,
            Events.Description,
            Events.EventDateTime,
            Users.Username AS OrganizerName,
            Locations.Address,
            Locations.City,
            Locations.State,
            Locations.PostalCode,
            Categories.Name AS CategoryName
        FROM Events
        JOIN Users ON Events.OrganizerID = Users.UserID
        JOIN Locations ON Events.LocationID = Locations.LocationID
        LEFT JOIN Categories ON Events.CategoryID = Categories.CategoryID
        WHERE Events.EventID = ? AND Events.ApprovalStatus = 'Approved'
    ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {

            // If category is null, fallback
            if (!$event['CategoryName']) {
                $event['CategoryName'] = "Uncategorized";
            }

            // ---------------------------------------------------------
            // FETCH TOTAL CAPACITY
            // ---------------------------------------------------------
            $capStmt = $pdo->prepare("
                SELECT SUM(TotalCapacity) AS TotalCapacity
                FROM TicketTypes
                WHERE EventID = ?
            ");
            $capStmt->execute([$id]);
            $capacityData = $capStmt->fetch(PDO::FETCH_ASSOC);

            $totalCapacity = $capacityData['TotalCapacity'] ?? 0;

            // ---------------------------------------------------------
            // FETCH TOTAL TICKETS SOLD
            // ---------------------------------------------------------
            $soldStmt = $pdo->prepare("
                SELECT COUNT(*) AS TicketsSold
                FROM Tickets
                JOIN TicketTypes ON Tickets.TicketTypeID = TicketTypes.TicketTypeID
                WHERE TicketTypes.EventID = ?
            ");
            $soldStmt->execute([$id]);
            $soldData = $soldStmt->fetch(PDO::FETCH_ASSOC);

            $ticketsSold = $soldData['TicketsSold'] ?? 0;

            // Remaining tickets
            $ticketsAvailable = max(0, $totalCapacity - $ticketsSold);

            // ---------------------------------------------------------
            // MINIMUM PRICE
            // ---------------------------------------------------------
            $priceStmt = $pdo->prepare("
                SELECT MIN(Price) AS MinPrice
                FROM TicketTypes
                WHERE EventID = ?
            ");
            $priceStmt->execute([$id]);
            $priceData = $priceStmt->fetch(PDO::FETCH_ASSOC);

            $startingPrice = $priceData['MinPrice'] ?? 0;
        }
    } catch (PDOException $e) {
        error_log("Database error fetching event details: " . $e->getMessage());
        $event = null;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>
        <?= $event ? htmlspecialchars($event['Title']) : "Event Not Found" ?>
    </title>

    <link rel="stylesheet" href="/Css_sri/global.css">
    <link rel="stylesheet" href="/Css_sri/event.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

<div class="container mt-5 mb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../events.php">Events</a></li>
            <li class="breadcrumb-item active">
                <?= $event ? htmlspecialchars($event['Title']) : "Event Not Found" ?>
            </li>
        </ol>
    </nav>

    <?php if (!$event): ?>

        <!-- ERROR STATE -->
        <div class="alert alert-danger">
            <h4>Event Not Found</h4>
            <a href="../events.php" class="btn btn-primary">← Back to Events</a>
        </div>

    <?php else: ?>

        <!-- EVENT IMAGE PLACEHOLDER -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-light text-center p-5"
                     style="border-radius: 8px; min-height: 300px; display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="#ccc" viewBox="0 0 16 16">
                        <path d="M14.5 1a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-12A1.5 1.5 0 0 1 1.5 1h13zm0 1h-13a.5.5 0 0 0-.5.5v8l2.775-2.776a.5.5 0 0 1 .707 0l2.69 2.69a.5.5 0 0 0 .707-.707l-2.19-2.19a.5.5 0 0 1 0-.707l5.477-5.477a.5.5 0 0 1 .707 0l1.621 1.621V2.5a.5.5 0 0 0-.5-.5zm5 11.5v-5h-1v5h1zm-2 0v-3h-1v3h1zm-2 0v-1h-1v1h1zm2-4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- TITLE + CATEGORY -->
        <div class="row mb-4">
            <div class="col-12">
                <h1><?= htmlspecialchars($event['Title']) ?></h1>
                <p>
                    <span class="badge bg-info text-dark">
                        <?= htmlspecialchars($event['CategoryName']) ?>
                    </span>
                </p>
                <p class="text-muted">Event ID: <?= htmlspecialchars($event['EventID']) ?></p>
            </div>
        </div>

        <!-- DETAILS -->
        <div class="row mb-4">

            <!-- LEFT -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">

                        <h5 class="card-title">Event Information</h5>

                        <div class="mb-3">
                            <strong>Date & Time:</strong><br>
                            <?= date("M d, Y h:i A", strtotime($event['EventDateTime'])) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Location:</strong><br>
                            <?= htmlspecialchars($event['Address']) ?>,
                            <?= htmlspecialchars($event['City']) ?>,
                            <?= htmlspecialchars($event['State']) ?>
                            <?= htmlspecialchars($event['PostalCode']) ?>
                        </div>

                        <div class="mb-3">
                            <strong>Organizer:</strong><br>
                            <?= htmlspecialchars($event['OrganizerName']) ?>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-md-6">
                <div class="card mb-3 border-primary">
                    <div class="card-body">

                        <h5 class="card-title">Ticket Information</h5>

                        <div class="mb-3">
                            <strong>Starting Price:</strong><br>
                            <span class="h4 text-primary">
                                $<?= number_format($startingPrice, 2) ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>Tickets Available:</strong><br>

                            <?php if ($ticketsAvailable > 0): ?>
                                <span class="badge bg-success">
                                    <?= $ticketsAvailable ?> Available
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger">Sold Out</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($ticketsAvailable > 0): ?>
                            <button class="btn btn-success w-100"
                                    onclick="alert('Ticket purchase coming soon!')">
                                Buy Ticket
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>
                                Sold Out
                            </button>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>

        <!-- DESCRIPTION -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Description</h5>
                        <p><?= nl2br(htmlspecialchars($event['Description'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- BACK -->
        <a href="../events.php" class="btn btn-outline-primary">← Back to Events</a>

    <?php endif; ?>

</div>

</body>
</html>

<?php
require "../lib/db.php";
session_start();

// Temporary Organizer until login is implemented
// TODO: replace with session-based organizer ID when auth is ready
$organizerID = 2;

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch Locations & Categories with error handling
$locations = [];
$categories = [];
$message = "";
try {
    $locStmt = $pdo->query("SELECT LocationID, Name, Address, City, State, PostalCode FROM Locations ORDER BY Name");
    $locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);

    $catStmt = $pdo->query("SELECT CategoryID, Name, Description FROM Categories ORDER BY Name ASC");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('DB error fetching locations/categories: ' . $e->getMessage());
    $message = "<div class='alert alert-danger'>Error loading form data. Try again later.</div>";
}

// Handle Form Submission
// Preserve old values on error
$old = ['title' => '', 'description' => '', 'category' => '', 'location' => '', 'date_time' => '', 'duration' => ''];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF check
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "<div class='alert alert-danger'>Invalid form submission (CSRF).</div>";
    } else {
        $title = trim($_POST["title"] ?? '');
        $desc = trim($_POST["description"] ?? '');
        $categoryID = $_POST["category"] ?? '';
        $locationID = $_POST["location"] ?? '';
        $datetime = $_POST["date_time"] ?? '';
        $duration = $_POST["duration"] ?? '';

        $old = ['title' => $title, 'description' => $desc, 'category' => $categoryID, 'location' => $locationID, 'date_time' => $datetime, 'duration' => $duration];

        // Basic required checks
        if ($title === '' || $locationID === '' || $datetime === '') {
            $message = "<div class='alert alert-danger'>Title, Location, and Date/Time are required.</div>";
        }

        // Whitelist checks for category and location
        $valid_cat_ids = array_column($categories, 'CategoryID');
        $valid_loc_ids = array_column($locations, 'LocationID');

        if ($categoryID !== '' && !in_array($categoryID, $valid_cat_ids)) {
            $message = "<div class='alert alert-danger'>Invalid category selected.</div>";
        }
        if (!in_array($locationID, $valid_loc_ids)) {
            $message = "<div class='alert alert-danger'>Invalid location selected.</div>";
        }

        // Validate datetime-local format (accepts with or without seconds)
        $dt = DateTime::createFromFormat('Y-m-d\TH:i', $datetime);
        if (!$dt) {
            $dt = DateTime::createFromFormat('Y-m-d\TH:i:s', $datetime);
        }
        if (!$dt) {
            $message = "<div class='alert alert-danger'>Invalid date/time format.</div>";
        }

        // Duration
        if ($duration === '') {
            $duration = null;
        } else {
            $duration = (int)$duration;
            if ($duration < 0) {
                $message = "<div class='alert alert-danger'>Duration must be 0 or greater.</div>";
            }
        }

        // If no errors so far, insert
        if ($message === "") {
            try {
                $sql = "
                    INSERT INTO Events (
                        OrganizerID, LocationID, CategoryID, Title, Description,
                        EventDateTime, DurationMinutes, ApprovalStatus
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $organizerID,
                    $locationID,
                    $categoryID !== '' ? $categoryID : null,
                    $title,
                    $desc,
                    $dt->format('Y-m-d H:i:s'),
                    $duration
                ]);

                header("Location: organiser_dashboard.php?created=1");
                exit;
            } catch (PDOException $e) {
                error_log('DB error inserting event: ' . $e->getMessage());
                $message = "<div class='alert alert-danger'>Unable to create event. Please try again later.</div>";
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Event</title>
    <link href="/Css_sri/global.css" rel="stylesheet">
    <link href="/Css_sri/event.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h1>Create Event</h1>

    <?= $message ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="mb-3">
            <label class="form-label">Title *</label>
            <input name="title" class="form-control" required value="<?= htmlspecialchars($old['title']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($old['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">— None —</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['CategoryID'] ?>" <?= ($old['category'] == $cat['CategoryID']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['Name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Location *</label>
            <select name="location" class="form-select" required>
                <option value="">— Select —</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc['LocationID'] ?>" <?= ($old['location'] == $loc['LocationID']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($loc['Name']) ?> — <?= htmlspecialchars($loc['City']) ?>, <?= htmlspecialchars($loc['State']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Date & Time *</label>
            <input type="datetime-local" name="date_time" class="form-control" required value="<?= htmlspecialchars($old['date_time']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Duration (minutes)</label>
            <input type="number" name="duration" class="form-control" min="0" value="<?= htmlspecialchars($old['duration']) ?>">
        </div>

        <button class="btn btn-primary">Create Event</button>
        <a href="organiser_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>

</div>
</body>
</html>
    </body>
    </html>

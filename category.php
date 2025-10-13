<?php
// Step 1: Sample data — replace later with database or API
$shows = [
    [
        "name" => "Welcome to MyTicket"
    ],
    [
        "name" => "The Phantom of the Opera",
        "type" => "Musical",
        "date" => "2025-10-12",
        "venue" => "Grand Theater",
        "price" => "$85",
        "image" => "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/28/f0/e3/d0/phantom-of-the-opera.jpg?w=900&h=500&s=1"
    ],
    [
        "name" => "Lakers vs Warriors",
        "type" => "Basketball",
        "date" => "2025-10-15",
        "venue" => "Crypto.com Arena",
        "price" => "$150",
        "image" => "https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=600&q=60"
    ],
    [
        "name" => "Coldplay World Tour",
        "type" => "Concert",
        "date" => "2025-10-20",
        "venue" => "SoFi Stadium",
        "price" => "$120",
        "image" => "https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?auto=format&fit=crop&w=600&q=60"
    ],
    [
        "name" => "Hamilton",
        "type" => "Theater",
        "date" => "2025-10-25",
        "venue" => "Broadway Stage",
        "price" => "$90",
        "image" => "https://images.unsplash.com/photo-1523731407965-2430cd12f5e4?auto=format&fit=crop&w=600&q=60"
    ],
    [
        "name" => "Taylor Swift Eras Tour",
        "type" => "Concert",
        "date" => "2025-11-02",
        "venue" => "Madison Square Garden",
        "price" => "$200",
        "image" => "https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=600&q=60"
    ]
];

// Step 2: Search logic (filter by name/type)
$search = $_GET['search'] ?? '';
if ($search) {
    $shows = array_filter($shows, function($show) use ($search) {
        return stripos($show['name'], $search) !== false || stripos($show['type'], $search) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Shows & Games</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f3f5f9;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            margin: 0;
        }
        form {
            text-align: center;
            margin: 20px 0;
        }
        input[type="text"] {
            padding: 10px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 14px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 20px;
        }
        .card {
            background-color: white;
            width: 300px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
        }
        .card h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 8px;
        }
        .card p {
            margin: 6px 0;
            color: #555;
            font-size: 14px;
        }
        .price {
            color: #007bff;
            font-weight: bold;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            margin-top: 10px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #218838;
        }
        .no-results {
            text-align: center;
            color: #777;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<h1>Available Shows & Games</h1>

<!-- 🔍 Search Bar -->
<form method="GET">
    <input type="text" name="search" placeholder="Search by name or type..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<!-- 🎟️ Show Cards -->
<div class="container">
    <?php if (count($shows) > 0): ?>
        <?php foreach ($shows as $show): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($show['image']) ?>" alt="<?= htmlspecialchars($show['name']) ?>">
                <div class="card-content">
                    <h2><?= htmlspecialchars($show["name"]) ?></h2>
                    <p><strong>Type:</strong> <?= htmlspecialchars($show["type"]) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($show["date"]) ?></p>
                    <p><strong>Venue:</strong> <?= htmlspecialchars($show["venue"]) ?></p>
                    <p class="price"><strong>Price:</strong> <?= htmlspecialchars($show["price"]) ?></p>
                    <a href="book.php?show=<?= urlencode($show['name']) ?>" class="button">Book Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-results">No shows found matching "<?= htmlspecialchars($search) ?>".</p>
    <?php endif; ?>
</div>

</body>
</html>

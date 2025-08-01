<?php
session_start();
include '../config/db.php';

// Check DB connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Restrict access to only visitors
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

// Fetch all exhibitions
$exhibitions = $conn->query("SELECT * FROM exhibitions ORDER BY start_date DESC");
if (!$exhibitions) {
    die("Error fetching exhibitions: " . $conn->error);
}

// Fetch artworks categorized by exhibition
$query = "
    SELECT 
        artworks.title AS artwork_title,
        artworks.image,
        artworks.medium,
        artworks.year_created,
        exhibitions.title AS exhibition_title,
        exhibitions.id AS exhibition_id
    FROM artworks
    JOIN exhibition_artworks ea ON artworks.id = ea.artwork_id
    JOIN exhibitions ON ea.exhibition_id = exhibitions.id
    ORDER BY exhibitions.title, artworks.title
";
$artworks_result = $conn->query($query);
if (!$artworks_result) {
    die("Error fetching artworks: " . $conn->error);
}

$artworks = [];
while ($row = $artworks_result->fetch_assoc()) {
    $artworks[$row['exhibition_title']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Gallery</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        header {
            background: linear-gradient(90deg, #2c3e50, #4ca1af);
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        h2 {
            text-align: center;
            margin: 30px 0 10px;
            font-size: 28px;
        }

        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 30px;
            max-width: 1400px;
            margin: auto;
        }

        .category {
            width: 320px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .category:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .category h3 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .artwork {
            text-align: center;
            margin-bottom: 20px;
        }

        .artwork img {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .artwork img:hover {
            transform: scale(1.05);
        }

        .artwork p {
            font-size: 14px;
            margin-top: 10px;
            color: #555;
        }

        .artwork p strong {
            font-size: 16px;
            color: #111;
        }

        @media (max-width: 768px) {
            .category {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>🎨 Welcome to the Visitor Gallery</h1>
    <p>Explore beautiful artworks featured in our exhibitions</p>
</header>

<h2>Exhibitions & Artworks</h2>

<div class="gallery-container">
    <?php if (!empty($artworks)): ?>
        <?php foreach ($artworks as $exhibition_title => $exhibition_artworks): ?>
            <div class="category">
                <h3><?= htmlspecialchars($exhibition_title) ?></h3>
                <?php foreach ($exhibition_artworks as $artwork): ?>
                    <div class="artwork">
                        <img src="<?= htmlspecialchars($artwork['image'] ?: 'images/default.jpg') ?>" alt="<?= htmlspecialchars($artwork['artwork_title']) ?>">
                        <p><strong><?= htmlspecialchars($artwork['artwork_title']) ?></strong></p>
                        <p><em><?= htmlspecialchars($artwork['medium']) ?>, <?= htmlspecialchars($artwork['year_created']) ?></em></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; width: 100%;">No artworks found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
session_start();
include '../config/db.php';

// Optional: Restrict access to only visitors
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

// Fetch all exhibitions
$exhibitions = $conn->query("SELECT * FROM exhibitions ORDER BY start_date DESC");

// Fetch artworks categorized by exhibition
$query = "
    SELECT 
        artworks.title AS artwork_title,
        artworks.image_path,
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }
        .category {
            width: 30%;
            margin: 15px 0;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .category h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .artwork {
            text-align: center;
            margin-bottom: 15px;
        }
        .artwork img {
            width: 100%;
            max-width: 200px;
            height: auto;
            border-radius: 8px;
        }
        .artwork p {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to the Visitor Gallery</h1>
</header>

<h2>Explore Artworks by Exhibition</h2>

<div class="gallery-container">
    <?php foreach ($artworks as $exhibition_title => $exhibition_artworks): ?>
        <div class="category">
            <h3><?= htmlspecialchars($exhibition_title) ?></h3>
            <?php foreach ($exhibition_artworks as $artwork): ?>
                <div class="artwork">
                    <img src="<?= htmlspecialchars($artwork['image_path']) ?>" alt="<?= htmlspecialchars($artwork['artwork_title']) ?>">
                    <p><strong><?= htmlspecialchars($artwork['artwork_title']) ?></strong></p>
                    <p><em><?= htmlspecialchars($artwork['medium']) ?>, <?= htmlspecialchars($artwork['year_created']) ?></em></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>

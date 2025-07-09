<?php
include '../config/db.php';

$artist_id = intval($_GET['id']); // Sanitize the input
$artist = $conn->query("SELECT name FROM users WHERE id = $artist_id")->fetch_assoc();
$artworks = $conn->query("SELECT * FROM artworks WHERE artist_id = $artist_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Artworks by <?= htmlspecialchars($artist['name']) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .artwork-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .artwork-title {
            font-weight: bold;
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .artwork-info {
            margin-top: 5px;
            color: #555;
        }

        h2 {
            color: #007bff;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Artworks by <?= htmlspecialchars($artist['name']) ?></h2>

    <div class="row">
        <?php while ($art = $artworks->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="artwork-card">
                    <div class="artwork-title"><?= htmlspecialchars($art['title']) ?></div>
                    <div class="artwork-info">Medium: <?= htmlspecialchars($art['medium']) ?></div>
                    <div class="artwork-info">Year: <?= htmlspecialchars($art['year_created']) ?></div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
include '../config/db.php';

$artist_id = intval($_GET['id']); // Sanitize input
$artist = $conn->query("SELECT name FROM users WHERE id = $artist_id")->fetch_assoc();
$artworks = $conn->query("SELECT * FROM artworks WHERE artist_id = $artist_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Artworks by <?= htmlspecialchars($artist['name']) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .page-header {
            margin: 40px 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h2 {
            color: #007bff;
            font-weight: 700;
        }

        .btn-back {
            background-color: #2980b9;
            color: white;
            font-weight: 600;
            border-radius: 5px;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #1f618d;
            color: white;
        }

        .artwork-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
            padding: 20px;
            margin-bottom: 30px;
            transition: transform 0.2s ease;
        }

        .artwork-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgb(0 0 0 / 0.15);
        }

        .artwork-title {
            font-weight: 700;
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .artwork-info {
            color: #555;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        @media (max-width: 576px) {
            .page-header {
                flex-direction: column;
                gap: 10px;
            }

            .btn-back {
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <h2>Artworks by <?= htmlspecialchars($artist['name']) ?></h2>
        <a href="explore_artists.php" class="btn-back">← Back to Artists</a>
    </div>

    <div class="row">
        <?php if ($artworks && $artworks->num_rows > 0): ?>
            <?php while ($art = $artworks->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="artwork-card">
                        <div class="artwork-title"><?= htmlspecialchars($art['title']) ?></div>
                        <div class="artwork-info">Medium: <?= htmlspecialchars($art['medium']) ?></div>
                        <div class="artwork-info">Year: <?= htmlspecialchars($art['year_created']) ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">No artworks found for this artist.</p>
        <?php endif; ?>
    </div>

</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

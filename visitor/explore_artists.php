<?php
include '../config/db.php';

$result = $conn->query("SELECT * FROM users WHERE role = 'artist'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Artists</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-header {
            margin: 40px 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h2 {
            color: #2c3e50;
            font-weight: 700;
        }

        .btn-dashboard {
            background-color: #2980b9;
            color: white;
            font-weight: 600;
            border-radius: 5px;
            padding: 8px 16px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .btn-dashboard:hover {
            background-color: #1f618d;
            color: white;
        }

        .artist-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s ease;
        }

        .artist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgb(0 0 0 / 0.15);
        }

        .artist-image {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #ddd;
            flex-shrink: 0;
            background: #eee;
        }

        .artist-details h5 {
            margin-bottom: 10px;
            color: #34495e;
            font-weight: 700;
        }

        .artist-details p {
            color: #6c757d;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .btn-view-artworks {
            background-color: #3498db;
            color: white;
            font-weight: 600;
            border-radius: 5px;
            padding: 7px 14px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view-artworks:hover {
            background-color: #2980b9;
            color: white;
        }

        @media (max-width: 576px) {
            .artist-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .artist-image {
                width: 100%;
                height: auto;
                max-height: 200px;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <h2>Artists</h2>
        <a href="dashboard.php" class="btn-dashboard">← Back to Dashboard</a>
    </div>

    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($artist = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="artist-card">
                        <img 
                            src="<?= htmlspecialchars($artist['image'] ?? 'default.jpg') ?>" 
                            alt="<?= htmlspecialchars($artist['name']) ?> Image" 
                            class="artist-image"
                            onerror="this.src='default.jpg';"
                        >
                        <div class="artist-details">
                            <h5><?= htmlspecialchars($artist['name']) ?></h5>
                            <p>Email: <?= htmlspecialchars($artist['email']) ?></p>
                            <a href="view_artist_artworks.php?id=<?= $artist['id'] ?>" class="btn-view-artworks">View Artworks</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">No artists found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

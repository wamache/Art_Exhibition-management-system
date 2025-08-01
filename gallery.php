<?php 
session_start();
include 'config/db.php';

// Fetch artworks with optional artist name
$query = "
    SELECT a.id, a.title, a.image, a.description, u.name AS artist_name
    FROM artworks a
    LEFT JOIN users u ON a.artist_id = u.id
    ORDER BY a.id DESC
";

$artworks = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Art Gallery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- GLightbox CSS -->
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 20px;
            min-height: 100vh;
            position: relative;
            color: #fff;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .gallery-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 30px 30px 30px;
            border-radius: 10px;
            color: #333;
            max-width: 1200px;
            margin: auto;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #343a40;
        }

        .card-img-top {
            object-fit: cover;
            height: 200px;
            transition: transform 0.3s ease;
            width: 100%;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .card-img-top:hover {
            transform: scale(1.05);
        }

        .btn-back {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background-color: rgba(255,255,255,0.8);
            border-radius: 5px;
            padding: 6px 12px;
            font-weight: 600;
            color: #333;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #e2e6ea;
            color: #000;
        }
    </style>
</head>
<body>

<a href="index.php" class="btn-back">&larr; Back to Dashboard</a>

<div class="gallery-container">
    <h1>Art Gallery</h1>
    <?php if ($artworks && $artworks->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($art = $artworks->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card shadow-sm">
                        <a href="<?= htmlspecialchars($art['image']) ?>" class="glightbox" data-title="<?= htmlspecialchars($art['title']) ?> by <?= htmlspecialchars($art['artist_name'] ?? 'Unknown Artist') ?>" data-description="<?= htmlspecialchars($art['description']) ?>">
                            <img src="<?= htmlspecialchars($art['image']) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="card-img-top" loading="lazy" />
                        </a>
                        <div class="card-body">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($art['title']) ?></h5>
                            <p class="card-text text-muted small mb-0"><?= htmlspecialchars($art['artist_name'] ?? 'Unknown Artist') ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-center fst-italic text-muted">No artworks found.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        zoomable: true,
    });
</script>

</body>
</html>

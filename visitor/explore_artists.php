<?php
include '../config/db.php';

$result = $conn->query("SELECT * FROM users WHERE role = 'artist'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Artists</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Your custom styles -->
    <style>
        .artist-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .artist-image {
            width: 100%;
            max-width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .artist-details h5 {
            margin-top: 0;
            color: #34495e;
        }

        .artist-details p {
            color: #555;
        }

        .btn-view {
            background-color: #3498db;
            color: white;
        }

        .btn-view:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-primary">Artists</h2>

    <div class="row">
        <?php while ($artist = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="artist-card d-flex">
                    <!-- Image -->
                    <img class="artist-image me-3" src="<?= htmlspecialchars($artist['image'] ?? 'default.jpg') ?>" alt="Artist Image">
                    
                    <!-- Details -->
                    <div class="artist-details">
                        <h5><?= htmlspecialchars($artist['name']) ?></h5>
                        <p>Email: <?= htmlspecialchars($artist['email']) ?></p>
                        <a href="view_artist_artworks.php?id=<?= $artist['id'] ?>" class="btn btn-view btn-sm">View Artworks</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Bootstrap JS and jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: jQuery interaction example -->
<script>
    $(document).ready(function() {
        $('.btn-view').on('click', function() {
            console.log("Viewing artist's artworks...");
            // Optional: Add your custom interaction here
        });
    });
</script>

</body>
</html>

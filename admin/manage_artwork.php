<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
include '../config/db.php';

$artworks = $conn->query("SELECT artworks.*, users.name AS artist_name 
                          FROM artworks 
                          JOIN artists ON artworks.artist_id = artists.id
                          JOIN users ON artists.user_id = users.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Artworks</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Artworks</h2>
        <a href="add_artwork.php" class="btn btn-primary">Add New Artwork</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Medium</th>
                    <th>Year</th>
                    <th>Artist</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $artworks->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['medium']) ?></td>
                    <td><?= $row['year_created'] ?></td>
                    <td><?= htmlspecialchars($row['artist_name']) ?></td>
                    <td>
                        <a href="edit_artwork.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_artwork.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this artwork?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

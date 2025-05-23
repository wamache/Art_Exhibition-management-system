<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
include '../config/db.php';

// Fetch all users who are artists
$artists = $conn->query("SELECT * FROM users WHERE role = 'artist'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Artists</title>

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
        <h2>Manage Artists</h2>
        <a href="add_artist.php" class="btn btn-primary">Add New Artist</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Bio</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($artist = $artists->fetch_assoc()): ?>
                <tr>
                    <td><?= $artist['id'] ?></td>
                    <td><?= htmlspecialchars($artist['name']) ?></td>
                    <td><?= htmlspecialchars($artist['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($artist['bio'] ?? '')) ?></td>
                    <td>
                        <a href="edit_artist.php?id=<?= $artist['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_artist.php?id=<?= $artist['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this artist?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

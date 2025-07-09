<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM artworks WHERE artist_id = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Artworks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 40px;
        }
        .btn-space {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-primary">My Artworks</h2>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
        <div class="alert alert-success">Artwork deleted successfully!</div>
    <?php endif; ?>

    <a href="add_artwork.php" class="btn btn-success mb-3">Add New Artwork</a>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Year Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($art = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($art['title']) ?></td>
                    <td><?= htmlspecialchars($art['year_created']) ?></td>
                    <td>
                        <a href="edit_artwork.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-warning btn-space">Edit</a>
                        <a href="delete_artwork.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this artwork?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php $stmt->close(); ?>
</div>

</body>
</html>

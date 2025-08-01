<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$user_id = $_SESSION['user']['id'];

// Fetch artist ID from `artists` table
$stmt = $conn->prepare("SELECT id FROM artists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$artist_result = $stmt->get_result();
$artist = $artist_result->fetch_assoc();
$artist_id = $artist['id'] ?? null;
$stmt->close();

if (!$artist_id) {
    die("Artist profile not found.");
}

// Fetch artworks
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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: url('https://www.transparenttextures.com/patterns/fabric-of-squares.png'), linear-gradient(120deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            margin-top: 60px;
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 25px rgba(0,0,0,0.05);
        }
        h2 {
            color: #2c3e50;
            font-weight: 700;
        }
        .btn-space {
            margin-right: 5px;
        }
        .table th {
            background-color: #eaf0f6;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">🎨 My Artworks</h2>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] === 'true'): ?>
        <div class="alert alert-success">Artwork deleted successfully!</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="dashboard.php" class="btn btn-outline-secondary back-btn">← Back to Dashboard</a>
        <a href="add_artwork.php" class="btn btn-primary">+ Add New Artwork</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Year Created</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">No artworks found.</td>
                </tr>
            <?php else: ?>
                <?php while ($art = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($art['title']) ?></td>
                        <td><?= htmlspecialchars($art['year_created']) ?></td>
                        <td><?= htmlspecialchars($art['status']) ?></td>
                        <td>
                            <a href="edit_artwork.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-warning btn-space">Edit</a>
                            <a href="delete_artwork.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this artwork?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

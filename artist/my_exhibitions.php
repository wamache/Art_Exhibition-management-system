<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];

// Use prepared statement for security
$stmt = $conn->prepare("
    SELECT DISTINCT exhibitions.*
    FROM exhibitions
    JOIN exhibition_artworks ON exhibitions.id = exhibition_artworks.exhibition_id
    JOIN artworks ON exhibition_artworks.artwork_id = artworks.id
    WHERE artworks.artist_id = ?
");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$exhibitions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Exhibitions</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    body { background-color: #f8f9fa; }
    .container { margin-top: 40px; }
</style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-primary">My Exhibitions</h2>
    <?php if ($exhibitions->num_rows === 0): ?>
        <div class="alert alert-info">You are not currently part of any exhibitions.</div>
    <?php else: ?>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Venue</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($ex = $exhibitions->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($ex['title']) ?></td>
                <td><?= htmlspecialchars($ex['date']) ?></td>
                <td><?= htmlspecialchars($ex['venue']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php
$stmt->close();
?>

</body>
</html>

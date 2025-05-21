<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id = $_POST['artist_id'];
    $title = $_POST['title'];
    $medium = $_POST['medium'];
    $year = $_POST['year'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO artworks (artist_id, title, medium, year_created, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $artist_id, $title, $medium, $year, $description);
    $stmt->execute();
    header("Location: manage_artwork.php");
    exit;
}

// Get artists
$artists = $conn->query("SELECT artists.id, users.name FROM artists JOIN users ON artists.user_id = users.id");
?>
<h2>Add New Artwork</h2>
<form method="post">
    Artist:
    <select name="artist_id" required>
        <?php while ($a = $artists->fetch_assoc()): ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
        <?php endwhile; ?>
    </select><br>
    Title: <input type="text" name="title" required><br>
    Medium: <input type="text" name="medium"><br>
    Year Created: <input type="text" name="year"><br>
    Description:<br>
    <textarea name="description"></textarea><br>
    <input type="submit" value="Add Artwork">
</form>

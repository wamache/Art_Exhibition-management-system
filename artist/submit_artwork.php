<?php
session_start();
include '../config/db.php';
$artist_id = $_SESSION['user']['id'];

$artworks = $conn->query("SELECT * FROM artworks WHERE artist_id = $artist_id");
$exhibitions = $conn->query("SELECT * FROM exhibitions");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artwork_id = $_POST['artwork_id'];
    $exhibition_id = $_POST['exhibition_id'];

    $stmt = $conn->prepare("INSERT IGNORE INTO exhibition_artworks (exhibition_id, artwork_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $exhibition_id, $artwork_id);
    $stmt->execute();

    echo "Submitted successfully!";
}
?>
<h2>Submit Artwork to Exhibition</h2>
<form method="post">
    Artwork:
    <select name="artwork_id">
        <?php while ($art = $artworks->fetch_assoc()): ?>
            <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['title']) ?></option>
        <?php endwhile; ?>
    </select><br>
    Exhibition:
    <select name="exhibition_id">
        <?php while ($ex = $exhibitions->fetch_assoc()): ?>
            <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['title']) ?></option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Submit">
</form>

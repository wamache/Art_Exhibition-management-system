<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $medium = $_POST['medium'];
    $year = $_POST['year'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE artworks SET title=?, medium=?, year_created=?, description=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $medium, $year, $description, $id);
    $stmt->execute();
    header("Location: manage_artworks.php");
    exit;
}

$artwork = $conn->query("SELECT * FROM artworks WHERE id = $id")->fetch_assoc();
?>
<h2>Edit Artwork</h2>
<form method="post">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($artwork['title']) ?>" required><br>
    Medium: <input type="text" name="medium" value="<?= htmlspecialchars($artwork['medium']) ?>"><br>
    Year Created: <input type="text" name="year" value="<?= $artwork['year_created'] ?>"><br>
    Description:<br>
    <textarea name="description"><?= htmlspecialchars($artwork['description']) ?></textarea><br>
    <input type="submit" value="Update Artwork">
</form>

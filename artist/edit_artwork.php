<?php
session_start();
include '../config/db.php';
$artist_id = $_SESSION['user']['id'];
$id = $_GET['id'];

// Fetch artwork
$art = $conn->query("SELECT * FROM artworks WHERE id = $id AND artist_id = $artist_id")->fetch_assoc();
if (!$art) die("Not found or access denied.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $medium = $_POST['medium'];
    $year_created = $_POST['year_created'];
    $description = $_POST['description'];

    // Image update
    $image = $art['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = "../uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $stmt = $conn->prepare("UPDATE artworks SET title=?, medium=?, year_created=?, description=?, image=? WHERE id=? AND artist_id=?");
    $stmt->bind_param("ssissii", $title, $medium, $year_created, $description, $image, $id, $artist_id);
    $stmt->execute();

    header("Location: manage_artworks.php");
    exit;
}
?>

<h2>Edit Artwork</h2>
<form method="post" enctype="multipart/form-data">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($art['title']) ?>"><br>
    Medium: <input type="text" name="medium" value="<?= htmlspecialchars($art['medium']) ?>"><br>
    Year Created: <input type="number" name="year_created" value="<?= $art['year_created'] ?>"><br>
    Description:<br>
    <textarea name="description" rows="4" cols="40"><?= htmlspecialchars($art['description']) ?></textarea><br>
    Current Image: <?= $art['image'] ? "<img src='../uploads/{$art['image']}' width='100'>" : "No image" ?><br>
    Replace Image: <input type="file" name="image"><br><br>
    <input type="submit" value="Update Artwork">
</form>

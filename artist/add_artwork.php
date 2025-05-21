<?php
session_start();
include '../config/db.php';
$artist_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $medium = $_POST['medium'];
    $year_created = $_POST['year_created'];
    $description = $_POST['description'];

    // Handle image upload
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = "../uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $stmt = $conn->prepare("INSERT INTO artworks (artist_id, title, medium, year_created, description, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississ", $artist_id, $title, $medium, $year_created, $description, $image);
    $stmt->execute();

    header("Location: manage_artworks.php");
    exit;
}
?>

<h2>Add New Artwork</h2>
<form method="post" enctype="multipart/form-data">
    Title: <input type="text" name="title" required><br>
    Medium: <input type="text" name="medium"><br>
    Year Created: <input type="number" name="year_created"><br>
    Description:<br>
    <textarea name="description" rows="4" cols="40"></textarea><br>
    Upload Image: <input type="file" name="image"><br><br>
    <input type="submit" value="Add Artwork">
</form>

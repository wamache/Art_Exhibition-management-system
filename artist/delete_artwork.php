<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];
$id = $_GET['id'];

// Ensure the artist owns the artwork
$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ? AND artist_id = ?");
$stmt->bind_param("ii", $id, $artist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $artwork = $result->fetch_assoc();
    // Optionally, delete the associated image file from the server
    if ($artwork['image']) {
        @unlink("../uploads/" . $artwork['image']);  // Delete the image file
    }

    // Delete the artwork from the database
    $stmt = $conn->prepare("DELETE FROM artworks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Redirect to the manage_artworks page with a success flag
    header("Location: manage_artworks.php?deleted=true");
    exit;
} else {
    echo "Artwork not found or access denied.";
}

$stmt->close();
?>

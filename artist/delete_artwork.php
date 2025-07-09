<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid artwork ID.");
}

// Ensure the artist owns the artwork
$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ? AND artist_id = ?");
$stmt->bind_param("ii", $id, $artist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $artwork = $result->fetch_assoc();
    $stmt->close();

    // Delete the associated image file safely
    if (!empty($artwork['image']) && file_exists("../uploads/" . $artwork['image'])) {
        @unlink("../uploads/" . $artwork['image']);
    }

    // Delete the artwork from the database
    $stmt = $conn->prepare("DELETE FROM artworks WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_artworks.php?deleted=true");
        exit;
    } else {
        die("Error deleting artwork: " . $stmt->error);
    }
} else {
    $stmt->close();
    die("Artwork not found or access denied.");
}
?>

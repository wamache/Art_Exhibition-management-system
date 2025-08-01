<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$user_id = $_SESSION['user']['id'];

// Get artist_id linked to user
$stmt = $conn->prepare("SELECT id FROM artists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$artist = $res->fetch_assoc();
$stmt->close();

if (!$artist) {
    die("Artist profile not found.");
}
$artist_id = (int)$artist['id'];

// Get artwork ID and validate
$artwork_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($artwork_id <= 0) {
    die("Invalid artwork ID.");
}

// Confirm artwork belongs to artist before deleting
$stmt = $conn->prepare("SELECT image FROM artworks WHERE id = ? AND artist_id = ?");
$stmt->bind_param("ii", $artwork_id, $artist_id);
$stmt->execute();
$res = $stmt->get_result();
$artwork = $res->fetch_assoc();
$stmt->close();

if (!$artwork) {
    die("Artwork not found or access denied.");
}

// Delete the image file if exists
$image_path = __DIR__ . '/../uploads/' . $artwork['image'];
if (file_exists($image_path)) {
    unlink($image_path);
}

// Delete the artwork from database
$stmt = $conn->prepare("DELETE FROM artworks WHERE id = ? AND artist_id = ?");
$stmt->bind_param("ii", $artwork_id, $artist_id);
if ($stmt->execute()) {
    $stmt->close();
    header("Location: manage_artworks.php?deleted=true");
    exit;
} else {
    $stmt->close();
    die("Failed to delete artwork.");
}

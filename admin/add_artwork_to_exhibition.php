<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$exhibition_id = $_POST['exhibition_id'];
$artwork_id = $_POST['artwork_id'];

// Check if already exists
$check = $conn->prepare("SELECT id FROM exhibition_artworks WHERE exhibition_id = ? AND artwork_id = ?");
$check->bind_param("ii", $exhibition_id, $artwork_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&error=exists");
    exit;
}

function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Insert new assignment
$stmt = $conn->prepare("INSERT INTO exhibition_artworks (exhibition_id, artwork_id) VALUES (?, ?)");
$stmt->bind_param("ii", $exhibition_id, $artwork_id);
$stmt->execute();
$stmt->close();

// Log the assignment action
log_action($conn, $_SESSION['user']['id'], 'Assigned artwork to exhibition', "Exhibition ID: $exhibition_id, Artwork ID: $artwork_id");

// Redirect back with success message
header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&success=1");
exit;
?>

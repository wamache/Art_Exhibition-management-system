<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Validate parameters
$exhibition_id = isset($_GET['exhibition_id']) ? (int)$_GET['exhibition_id'] : 0;
$artwork_id = isset($_GET['artwork_id']) ? (int)$_GET['artwork_id'] : 0;

if ($exhibition_id <= 0 || $artwork_id <= 0) {
    die("Invalid parameters.");
}

$stmt = $conn->prepare("DELETE FROM exhibition_artworks WHERE exhibition_id = ? AND artwork_id = ?");
$stmt->bind_param("ii", $exhibition_id, $artwork_id);

if ($stmt->execute()) {
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&success=1");
} else {
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&error=1");
}
exit;

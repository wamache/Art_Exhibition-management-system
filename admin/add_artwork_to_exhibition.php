<?php
session_start();
<<<<<<< HEAD
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$exhibition_id = $_POST['exhibition_id'];
$artwork_id = $_POST['artwork_id'];

// Check if already exists
$check = $conn->prepare("SELECT id FROM exhibition_artworks WHERE exhibition_id = ? AND artwork_id = ?");
=======

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Basic validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['exhibition_id'], $_POST['artwork_id'])) {
    die("Invalid request");
}

$exhibition_id = intval($_POST['exhibition_id']);
$artwork_id = intval($_POST['artwork_id']);

// Check if assignment already exists
$check = $conn->prepare("SELECT 1 FROM exhibition_artworks WHERE exhibition_id = ? AND artwork_id = ?");
if (!$check) {
    die("Prepare failed: " . $conn->error);
}

>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
$check->bind_param("ii", $exhibition_id, $artwork_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
<<<<<<< HEAD
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&error=exists");
    exit;
}

function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
=======
    $check->close();
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&error=exists");
    exit;
}
$check->close();

function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iss", $user_id, $action, $details);
        $stmt->execute();
        $stmt->close();
    }
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
}

// Insert new assignment
$stmt = $conn->prepare("INSERT INTO exhibition_artworks (exhibition_id, artwork_id) VALUES (?, ?)");
<<<<<<< HEAD
$stmt->bind_param("ii", $exhibition_id, $artwork_id);
$stmt->execute();
$stmt->close();

// Log the assignment action
log_action($conn, $_SESSION['user']['id'], 'Assigned artwork to exhibition', "Exhibition ID: $exhibition_id, Artwork ID: $artwork_id");

// Redirect back with success message
header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&success=1");
exit;
=======
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $exhibition_id, $artwork_id);
if ($stmt->execute()) {
    // Log the assignment action
    log_action($conn, $_SESSION['user']['id'], 'Assigned artwork to exhibition', "Exhibition ID: $exhibition_id, Artwork ID: $artwork_id");
    $stmt->close();
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&success=1");
    exit;
} else {
    $error = "Error inserting assignment: " . $stmt->error;
    $stmt->close();
    die($error);
}
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
?>

<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Sanitize input and use prepared statement
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Optional: log action before deletion
    function log_action($conn, $user_id, $action, $details = '') {
        $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $action, $details);
        $stmt->execute();
        $stmt->close();
    }

    // Log deletion
    log_action($conn, $_SESSION['user']['id'], 'delete_artwork', "Artwork ID: $id");

    // Use prepared statement for deletion
    $stmt = $conn->prepare("DELETE FROM artworks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to manage page
header("Location: manage_artworks.php");
exit;
?>

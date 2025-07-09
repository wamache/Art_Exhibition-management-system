<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Validate and sanitize input
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Optional: log the action before deletion
    function log_action($conn, $user_id, $action, $details = '') {
        $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $action, $details);
        $stmt->execute();
        $stmt->close();
    }

    log_action($conn, $_SESSION['user']['id'], 'delete_exhibition', "Deleted exhibition ID: $id");

    // Use a prepared statement for deletion
    $stmt = $conn->prepare("DELETE FROM exhibitions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect safely
header("Location: manage_exhibitions.php");
exit;
?>

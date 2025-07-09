<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Sanitize and validate ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Optional: log action
    function log_action($conn, $user_id, $action, $details = '') {
        $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $action, $details);
        $stmt->execute();
        $stmt->close();
    }

    log_action($conn, $_SESSION['user']['id'], 'delete_user', "Deleted user ID: $id");

    // Use a prepared statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_users.php");
exit;
?>

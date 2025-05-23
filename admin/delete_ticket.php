<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Optional: Logging function
function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Validate and sanitize ticket ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid ticket ID.");
}

// Fetch ticket info for logging
$stmt = $conn->prepare("SELECT buyer_name, price FROM tickets WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Ticket not found.");
}

// Delete the ticket
$stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Log deletion
    log_action($conn, $_SESSION['user']['id'], 'Deleted Ticket', "Buyer: {$ticket['buyer_name']}, Price: {$ticket['price']}");
    $stmt->close();
    header("Location: manage_tickets.php?deleted=1");
    exit;
} else {
    die("Error deleting ticket: " . $stmt->error);
}
?>

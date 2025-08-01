<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

$exhibition_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($exhibition_id <= 0) {
    die("Invalid exhibition ID.");
}

// Log action function
function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Begin transaction
$conn->begin_transaction();

try {
    // Delete artwork links first (child table)
    $stmt = $conn->prepare("DELETE FROM exhibition_artworks WHERE exhibition_id = ?");
    $stmt->bind_param("i", $exhibition_id);
    $stmt->execute();
    $stmt->close();

    // Delete the exhibition (parent table)
    $stmt = $conn->prepare("DELETE FROM exhibitions WHERE id = ?");
    $stmt->bind_param("i", $exhibition_id);
    $stmt->execute();
    $stmt->close();

    // Log deletion
    log_action($conn, $_SESSION['user']['id'], 'Deleted exhibition', "Exhibition ID: $exhibition_id");

    // Commit
    $conn->commit();

    // Redirect
    header("Location: manage_exhibitions.php?success=1");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Error deleting exhibition: " . $e->getMessage());
}
?>

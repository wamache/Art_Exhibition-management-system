<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $venue = $_POST['venue'];

    $stmt = $conn->prepare("INSERT INTO exhibitions (title, date, venue) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $date, $venue);
    $stmt->execute();
    header("Location: manage_exhibitions.php");
    exit;
}
?>
<h2>Add New Exhibition</h2>
<form method="post">
    Title: <input type="text" name="title" required><br>
    Date: <input type="date" name="date" required><br>
    Venue: <input type="text" name="venue" required><br>
    <input type="submit" value="Create Exhibition">
</form>

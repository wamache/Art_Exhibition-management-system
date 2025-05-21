<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$id = $_GET['id'];
$exhibition = $conn->query("SELECT * FROM exhibitions WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $venue = $_POST['venue'];

    $stmt = $conn->prepare("UPDATE exhibitions SET title=?, date=?, venue=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $date, $venue, $id);
    $stmt->execute();
    header("Location: manage_exhibitions.php");
    exit;
}
?>
<h2>Edit Exhibition</h2>
<form method="post">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($exhibition['title']) ?>" required><br>
    Date: <input type="date" name="date" value="<?= $exhibition['date'] ?>" required><br>
    Venue: <input type="text" name="venue" value="<?= htmlspecialchars($exhibition['venue']) ?>" required><br>
    <input type="submit" value="Update Exhibition">
</form>

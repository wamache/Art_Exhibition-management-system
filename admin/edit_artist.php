<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $bio = $_POST['bio'];
    $contact = $_POST['contact'];

    $stmt = $conn->prepare("UPDATE artists SET user_id=?, bio=?, contact=? WHERE id=?");
    $stmt->bind_param("issi", $user_id, $bio, $contact, $id);
    $stmt->execute();
    header("Location: manage_artists.php");
    exit;
}

$artist = $conn->query("SELECT * FROM artists WHERE id = $id")->fetch_assoc();
$users = $conn->query("SELECT * FROM users WHERE role = 'artist'");
?>
<h2>Edit Artist</h2>
<form method="post">
    User:
    <select name="user_id" required>
        <?php while ($user = $users->fetch_assoc()): ?>
            <option value="<?= $user['id'] ?>" <?= $user['id'] == $artist['user_id'] ? 'selected' : '' ?>><?= htmlspecialchars($user['name']) ?></option>
        <?php endwhile; ?>
    </select><br>
    Bio: <textarea name="bio" required><?= htmlspecialchars($artist['bio']) ?></textarea><br>
    Contact: <input type="text" name="contact" value="<?= htmlspecialchars($artist['contact']) ?>" required><br>
    <input type="submit" value="Update Artist">
</form>

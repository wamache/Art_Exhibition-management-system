<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $bio = $_POST['bio'];
    $contact = $_POST['contact'];

    $stmt = $conn->prepare("INSERT INTO artists (user_id, bio, contact) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $bio, $contact);
    $stmt->execute();
    header("Location: /project/art_exhibition/admin/manage_artists.php");
    exit;
}

$users = $conn->query("SELECT * FROM users WHERE role = 'artist'");
?>
<h2>Add New Artist</h2>
<form method="post">
    User:
    <select name="user_id" required>
        <?php while ($user = $users->fetch_assoc()): ?>
            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
        <?php endwhile; ?>
    </select><br>
    Bio: <textarea name="bio" required></textarea><br>
    Contact: <input type="text" name="contact" required><br>
    <input type="submit" value="Add Artist">
</form>

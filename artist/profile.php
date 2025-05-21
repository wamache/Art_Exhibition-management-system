<?php
session_start();
include '../config/db.php';

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $contact = $_POST['contact'];

    $stmt = $conn->prepare("UPDATE users SET name=?, bio=?, contact=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $bio, $contact, $user_id);
    $stmt->execute();

    $_SESSION['user']['name'] = $name;
    echo "Profile updated.";
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>
<h2>Edit Profile</h2>
<form method="post">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
    Bio: <textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea><br>
    Contact Info: <input type="text" name="contact" value="<?= htmlspecialchars($user['contact']) ?>"><br>
    <input type="submit" value="Update">
</form>

<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

$user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();
?>
<h2>Edit User</h2>
<form method="post">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
    Role:
    <select name="role" required>
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="artist" <?= $user['role'] == 'artist' ? 'selected' : '' ?>>Artist</option>
        <option value="visitor" <?= $user['role'] == 'visitor' ? 'selected' : '' ?>>Visitor</option>
    </select><br>
    <input type="submit" value="Update User">
</form>

<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
include '../config/db.php';

// Fetch all users who are artists
$artists = $conn->query("SELECT * FROM users WHERE role = 'artist'");
?>

<h2>Manage Artists</h2>
<a href="add_artist.php">Add New Artist</a>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Bio</th>
        <th>Actions</th>
    </tr>
    <?php while ($artist = $artists->fetch_assoc()): ?>
    <tr>
        <td><?= $artist['id'] ?></td>
        <td><?= htmlspecialchars($artist['name']) ?></td>
        <td><?= htmlspecialchars($artist['email']) ?></td>
        <td><?= nl2br(htmlspecialchars($artist['bio'] ?? '')) ?></td>
        <td>
            <a href="edit_artist.php?id=<?= $artist['id'] ?>">Edit</a> |
            <a href="delete_artist.php?id=<?= $artist['id'] ?>" onclick="return confirm('Are you sure you want to delete this artist?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

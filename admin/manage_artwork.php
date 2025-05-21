<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
include '../config/db.php';

$artworks = $conn->query("SELECT artworks.*, users.name AS artist_name 
                          FROM artworks 
                          JOIN artists ON artworks.artist_id = artists.id
                          JOIN users ON artists.user_id = users.id");
?>
<h2>Manage Artworks</h2>
<a href="add_artwork.php">Add New Artwork</a>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Medium</th>
        <th>Year</th>
        <th>Artist</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $artworks->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['medium']) ?></td>
        <td><?= $row['year_created'] ?></td>
        <td><?= htmlspecialchars($row['artist_name']) ?></td>
        <td>
            <a href="edit_artwork.php?id=<?= $row['id'] ?>">Edit</a> |
            <a href="delete_artwork.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this artwork?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

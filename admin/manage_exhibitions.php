<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Fetch all exhibitions
$exhibitions = $conn->query("SELECT * FROM exhibitions ORDER BY date DESC");
?>

<h2>Manage Exhibitions</h2>
<a href="add_exhibition.php">Add New Exhibition</a>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Venue</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php while ($ex = $exhibitions->fetch_assoc()): ?>
    <tr>
        <td><?= $ex['id'] ?></td>
        <td><?= htmlspecialchars($ex['title']) ?></td>
        <td><?= htmlspecialchars($ex['venue']) ?></td>
        <td><?= $ex['date'] ?></td>
        <td>
            <a href="edit_exhibition.php?id=<?= $ex['id'] ?>">Edit</a> |
            <a href="delete_exhibition.php?id=<?= $ex['id'] ?>" onclick="return confirm('Are you sure you want to delete this exhibition?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

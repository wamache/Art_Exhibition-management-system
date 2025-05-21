<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$tickets = $conn->query("SELECT tickets.*, exhibitions.title AS exhibition FROM tickets JOIN exhibitions ON tickets.exhibition_id = exhibitions.id");
?>
<h2>Manage Tickets</h2>
<a href="add_ticket.php">Add Ticket</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Price</th>
        <th>Exhibition</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $tickets->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['type'] ?></td>
        <td><?= $row['price'] ?></td>
        <td><?= htmlspecialchars($row['exhibition']) ?></td>
        <td>
            <a href="edit_ticket.php?id=<?= $row['id'] ?>">Edit</a> |
            <a href="delete_ticket.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this ticket?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

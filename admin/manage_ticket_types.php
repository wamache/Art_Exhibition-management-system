<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$types = $conn->query("
    SELECT tt.id, tt.type, tt.price, e.title AS exhibition_title
    FROM ticket_types tt
    JOIN exhibitions e ON tt.exhibition_id = e.id
");
?>
<h2>Ticket Types Linked to Exhibitions</h2>
<a href="add_ticket.php">Add New Ticket Type</a>
<table border="1">
<tr>
    <th>ID</th>
    <th>Type</th>
    <th>Price</th>
    <th>Exhibition</th>
    <th>Actions</th>
</tr>
<?php while ($t = $types->fetch_assoc()): ?>
<tr>
    <td><?= $t['id'] ?></td>
    <td><?= htmlspecialchars($t['type']) ?></td>
    <td>$<?= number_format($t['price'], 2) ?></td>
    <td><?= htmlspecialchars($t['exhibition_title']) ?></td>
    <td>
        <a href="edit_ticket.php?id=<?= $t['id'] ?>">Edit</a> |
        <a href="delete_ticket.php?id=<?= $t['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

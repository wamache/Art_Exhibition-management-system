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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Types</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Ticket Types Linked to Exhibitions</h2>
        <a href="add_ticket.php" class="btn btn-primary">Add New Ticket Type</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Exhibition</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($t = $types->fetch_assoc()): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['type']) ?></td>
                    <td>$<?= number_format($t['price'], 2) ?></td>
                    <td><?= htmlspecialchars($t['exhibition_title']) ?></td>
                    <td>
                        <a href="edit_ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

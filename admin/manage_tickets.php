<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Fetch tickets with exhibition titles
$sql = "SELECT tickets.*, exhibitions.title AS exhibition 
        FROM tickets 
        JOIN exhibitions ON tickets.exhibition_id = exhibitions.id
        ORDER BY tickets.id DESC";

$tickets = $conn->query($sql);
if (!$tickets) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Tickets</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            padding-top: 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 30px;
            font-weight: 700;
            color: #333;
        }
        .btn-back {
            margin-bottom: 20px;
        }
        table th, table td {
            vertical-align: middle !important;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="admin_dashboard.php" class="btn btn-secondary btn-back">&larr; Back to Dashboard</a>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Tickets</h2>
        <a href="add_ticket.php" class="btn btn-primary">Add Ticket</a>
    </div>

    <?php if ($tickets->num_rows === 0): ?>
        <p class="text-muted">No tickets found.</p>
    <?php else: ?>
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
                <?php while ($row = $tickets->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['type'] ?? 'N/A') ?></td>
                    <td>Ksh <?= number_format(floatval($row['price'] ?? 0), 2) ?></td>
                    <td><?= htmlspecialchars($row['exhibition'] ?? 'N/A') ?></td>
                    <td>
                        <a href="edit_ticket.php?id=<?= urlencode($row['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_ticket.php?id=<?= urlencode($row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

</body>
</html>

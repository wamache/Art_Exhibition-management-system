<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') die("Access denied");
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
    <meta charset="UTF-8" />
    <title>Ticket Types</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2, #6a11cb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            padding-top: 70px; /* space for fixed back button */
            color: #333;
        }
        .container {
            max-width: 960px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            margin: auto;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 10000;
            font-weight: 600;
            font-size: 16px;
            color: #000;
            background: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .back-btn:hover {
            background: #f0f0f0;
            color: #333;
        }
        table th, table td {
            vertical-align: middle !important;
        }
        @media (max-width: 575.98px) {
            .container {
                padding: 20px 20px;
            }
        }
    </style>
</head>
<body>

<a href="dashboard.php" class="back-btn" title="Back to Dashboard">&larr; Back</a>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Ticket Types Linked to Exhibitions</h2>
        <a href="add_ticket.php" class="btn btn-primary">Add New Ticket Type</a>
    </div>

    <div class="table-responsive shadow rounded">
        <table class="table table-bordered table-striped align-middle mb-0">
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
                <?php if ($types->num_rows > 0): ?>
                    <?php while ($t = $types->fetch_assoc()): ?>
                        <tr>
                            <td><?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['type']) ?></td>
                            <td>$<?= number_format($t['price'], 2) ?></td>
                            <td><?= htmlspecialchars($t['exhibition_title']) ?></td>
                            <td>
                                <a href="edit_ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete_ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this ticket type?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center fst-italic text-muted">No ticket types found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

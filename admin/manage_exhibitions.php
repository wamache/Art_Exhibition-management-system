<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Fetch all exhibitions ordered by start date (latest first)
$exhibitions = $conn->query("SELECT * FROM exhibitions ORDER BY start_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Exhibitions</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 40px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }
        h2 {
            color: #e0e0e0;
            font-weight: 700;
        }
        table {
            background-color: rgba(255, 255, 255, 0.9);
            color: #212529;
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead.table-dark th {
            background-color: #4a3c8c;
            border-color: #4a3c8c;
            color: #fff;
        }
        .btn-primary {
            background-color: #5a47ab;
            border: none;
        }
        .btn-primary:hover {
            background-color: #3f2f82;
        }
        .btn-warning {
            background-color: #f0ad4e;
            border: none;
            color: #212529;
        }
        .btn-danger {
            background-color: #d9534f;
            border: none;
        }
        .btn-danger:hover {
            background-color: #b52b27;
        }
        .btn-light {
            background-color: #fff;
            color: #333;
            border-radius: 6px;
            font-weight: 500;
        }
        .btn-light:hover {
            background-color: #e9ecef;
            color: #000;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Back Button -->
    <a href="dashboard.php" class="btn btn-light mb-3">&larr; Back to Dashboard</a>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Exhibitions</h2>
        <a href="add_exhibition.php" class="btn btn-primary">Add New Exhibition</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Dates</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $ex['id'] ?></td>
                        <td><?= htmlspecialchars($ex['title']) ?></td>
                        <td><?= htmlspecialchars($ex['location']) ?></td>
                        <td><?= htmlspecialchars($ex['start_date']) ?> to <?= htmlspecialchars($ex['end_date']) ?></td>
                        <td>
                            <a href="edit_exhibition.php?id=<?= $ex['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_exhibition.php?id=<?= $ex['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this exhibition?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

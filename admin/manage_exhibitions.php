<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Fetch all exhibitions
$exhibitions = $conn->query("SELECT * FROM exhibitions ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Exhibitions</title>

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
        <h2>Manage Exhibitions</h2>
        <a href="add_exhibition.php" class="btn btn-primary">Add New Exhibition</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Venue</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $ex['id'] ?></td>
                        <td><?= htmlspecialchars($ex['title']) ?></td>
                        <td><?= htmlspecialchars($ex['venue']) ?></td>
                        <td><?= $ex['date'] ?></td>
                        <td>
                            <a href="edit_exhibition.php?id=<?= $ex['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_exhibition.php?id=<?= $ex['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this exhibition?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

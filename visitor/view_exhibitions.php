<?php
include '../config/db.php';

$result = $conn->query("SELECT * FROM exhibitions ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exhibitions</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h2 {
            color: #2c3e50;
            margin: 30px 0 20px;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .table td, .table th {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2>Exhibitions</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Venue</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ex = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($ex['title']) ?></td>
                    <td><?= htmlspecialchars($ex['description']) ?></td>
                    <td><?= htmlspecialchars($ex['location']) ?></td>
                    <td><?= htmlspecialchars($ex['start_date']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

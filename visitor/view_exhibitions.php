<?php
include '../config/db.php';

// Fetch exhibitions ordered by start date descending
$result = $conn->query("SELECT * FROM exhibitions ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Exhibitions</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('https://images.unsplash.com/photo-1601979041343-3a9f9447c546?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.65);
            min-height: 100vh;
            padding: 40px 0;
        }

        .page-header {
            background-color: rgba(0, 123, 255, 0.9);
            color: white;
            padding: 20px 30px;
            margin-bottom: 30px;
            border-radius: 0 0 10px 10px;
        }

        .page-header h2 {
            margin: 0;
        }

        .back-btn {
            margin: 20px 0;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
        }

        .table {
            background-color: #ffffffee;
            border-radius: 10px;
            overflow: hidden;
        }

        .container {
            max-width: 1100px;
        }

        .table td, .table th {
            vertical-align: middle;
            color: #333;
        }
    </style>
</head>
<body>

<div class="overlay">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center shadow">
            <h2>All Exhibitions</h2>
            <a href="dashboard.php" class="btn btn-light fw-semibold">← Back to Dashboard</a>
        </div>

        <div class="table-responsive shadow">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Venue</th>
                        <th>Start Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($ex = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($ex['title']) ?></td>
                                <td><?= nl2br(htmlspecialchars($ex['description'])) ?></td>
                                <td><?= htmlspecialchars($ex['location']) ?></td>
                                <td><?= date('M d, Y', strtotime($ex['start_date'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No exhibitions available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

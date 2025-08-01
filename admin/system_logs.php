<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

$logs = $conn->query("
    SELECT l.*, u.name AS username
    FROM system_logs l
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY l.timestamp DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>System Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.unsplash.com/photo-1517433456452-f9633a875f6f?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
            background-size: cover;
            padding: 40px 20px;
            color: #212529;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        h1 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 30px;
            color: #343a40;
        }
        table {
            border-collapse: collapse;
        }
        thead.table-dark th {
            background-color: #343a40 !important;
            color: #fff !important;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #e9ecef;
            transition: background-color 0.3s ease;
        }
        /* Back button styling */
        .btn-back {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            font-weight: 600;
        }
        /* Responsive overflow for the table */
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>

    <a href="dashboard.php" class="btn btn-outline-dark btn-back">&larr; Back to Dashboard</a>

    <div class="container">
        <h1>System Logs</h1>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs && $logs->num_rows > 0): ?>
                        <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr>
                                <td><?= $log['id'] ?></td>
                                <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= htmlspecialchars($log['details']) ?></td>
                                <td><?= $log['timestamp'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center fst-italic text-muted">No logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

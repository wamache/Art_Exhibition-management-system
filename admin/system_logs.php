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
<html>
<head>
    <title>System Logs</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

<h1>System Logs</h1>
<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Action</th>
        <th>Details</th>
        <th>Timestamp</th>
    </tr>
    <?php while ($log = $logs->fetch_assoc()): ?>
    <tr>
        <td><?= $log['id'] ?></td>
        <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
        <td><?= htmlspecialchars($log['action']) ?></td>
        <td><?= htmlspecialchars($log['details']) ?></td>
        <td><?= $log['timestamp'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

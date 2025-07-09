<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            padding: 40px;
        }
        .admin-panel {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container admin-panel">
        <h2 class="text-primary">Admin Panel</h2>
        <p class="lead">Welcome, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>!</p>

        <!-- You can add navigation or admin links here -->
        <div class="mt-4">
            <a href="manage_users.php" class="btn btn-outline-primary">Manage Users</a>
            <a href="manage_artworks.php" class="btn btn-outline-success">Manage Artworks</a>
            <a href="reports.php" class="btn btn-outline-info">View Reports</a>
        </div>
    </div>
</body>
</html>

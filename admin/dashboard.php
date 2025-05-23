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
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS (requires Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
        }
        .content {
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
            <div class="position-sticky">
                <h4 class="text-white text-center mb-4">Admin Panel</h4>
                <a href="/project/art_exhibition/admin/add_users.php">Add Users</a>
                <a href="/project/art_exhibition/admin/add_artist.php">Add Artists</a>
                <a href="/project/art_exhibition/admin/manage_artwork.php">Manage Artworks</a>
                <a href="/project/art_exhibition/admin/manage_exhibitions.php">Manage Exhibitions</a>
                <a href="/project/art_exhibition/admin/manage_exhibition_artworks.php">Assign Artwork</a>
                <a href="/project/art_exhibition/admin/manage_ticket_types.php">Manage Tickets</a>
                <a href="/project/art_exhibition/admin/generate_reports.php">Generate Reports</a>
                <a href="/project/art_exhibition/admin/system_logs.php">View Logs</a>
                <a href="/project/art_exhibition/auth/logout.php" class="text-danger">Logout</a>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 content">
            <h1>Welcome, Admin <?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></h1>
            <p>Select an option from the sidebar to begin managing the system.</p>
        </main>
    </div>
</div>

</body>
</html>

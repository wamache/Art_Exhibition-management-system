<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

// Ticket statistics by exhibition
$ticketStats = [];
$sql = "
    SELECT e.title, COUNT(t.id) as tickets_sold
    FROM exhibitions e
    LEFT JOIN tickets t ON e.id = t.exhibition_id
    GROUP BY e.id
    ORDER BY tickets_sold DESC
    LIMIT 4
";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $ticketStats[] = $row;
    }
}

// For additional stats
$artistCount = $conn->query("SELECT COUNT(*) as total FROM artists")->fetch_assoc()['total'];
$artworkCount = $conn->query("SELECT COUNT(*) as total FROM artworks")->fetch_assoc()['total'];
$exhibitionCount = $conn->query("SELECT COUNT(*) as total FROM exhibitions")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
        .dashboard-cards .card {
            border-left: 5px solid #007bff;
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
                <a href="add_user.php">Add Users</a>
                <a href="add_artist.php">Add Artists</a>
                <a href="manage_artwork.php">Manage Artworks</a>
                <a href="manage_exhibitions.php">Manage Exhibitions</a>
                <a href="manage_exhibition_artworks.php">Assign Artwork</a>
                <a href="manage_ticket_types.php">Manage Tickets</a>
                <a href="generate_reports.php">Generate Reports</a>
                <a href="add_section.php">Add Sections</a>
                <a href="system_logs.php">View Logs</a>
                <a href="../logout.php" class="text-danger">Logout</a>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 content">
            <h1 class="mb-4">Welcome, Admin <?= htmlspecialchars($_SESSION['user']['name']) ?></h1>
            <p>Select an option from the sidebar to begin managing the system.</p>

            <!-- Dashboard Summary Cards -->
            <div class="row dashboard-cards mb-4">
                <?php foreach ($ticketStats as $stat): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($stat['title']) ?></h6>
                                <p class="card-text display-6"><?= $stat['tickets_sold'] ?></p>
                                <small class="text-muted">Tickets Sold</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- More Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-bg-light mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Artists</h5>
                            <p class="card-text fs-3"><?= $artistCount ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-light mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Artworks</h5>
                            <p class="card-text fs-3"><?= $artworkCount ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-light mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Exhibitions</h5>
                            <p class="card-text fs-3"><?= $exhibitionCount ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">System Activity Summary</h5>
                </div>
                <div class="card-body">
                    <p>Use the navigation menu to manage users, artworks, exhibitions, and system data.</p>
                    <ul>
                        <li>Monitor ticket sales for popular exhibitions.</li>
                        <li>Keep artist and artwork databases up-to-date.</li>
                        <li>Generate reports for performance analysis.</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
    <div class="bottom-bar">   

© 2025 AEMS. All rights reserved.
Designed for admins to manage, explore and enjoy exhibitions.

</div>

<style>
  .bottom-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #2980b9;
    color: white;
    padding: 10px 20px;
    text-align: center;
    font-weight: 600;
    box-shadow: 0 -2px 5px rgba(0,0,0,0.2);
    z-index: 1000; /* above other content */
  }
</style>
</div>

</body>
</html>

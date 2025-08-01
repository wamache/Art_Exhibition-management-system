<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

// Ticket statistics by exhibition (top 4)
$ticketStats = [];
$sql = "
    SELECT e.title, COUNT(t.id) AS tickets_sold
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

// Other counts
$artistCount = $conn->query("SELECT COUNT(*) as total FROM artists")->fetch_assoc()['total'];
$artworkCount = $conn->query("SELECT COUNT(*) as total FROM artworks")->fetch_assoc()['total'];
$exhibitionCount = $conn->query("SELECT COUNT(*) as total FROM exhibitions")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            overflow-y: auto;
        }
        .sidebar h4 {
            color: #fff;
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        .sidebar a {
            color: #ddd;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .sidebar a.text-danger {
            color: #dc3545 !important;
        }
        main.content {
            margin-left: 220px;
            padding: 30px;
            min-height: 100vh;
        }
        .dashboard-cards .card {
            border-left: 5px solid #007bff;
            transition: box-shadow 0.3s ease;
        }
        .dashboard-cards .card:hover {
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        }
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 220px;
            right: 0;
            background-color: #2980b9;
            color: white;
            padding: 10px 20px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<nav class="sidebar">
    <h4>Admin Panel</h4>
    <!-- <a href="add_user.php">Add Users</a> -->
     <a href="manage_users.php">Manage Users</a> 
    <a href="manage_artwork.php">Manage Artworks</a>
    <a href="manage_exhibitions.php">Manage Exhibitions</a>
    <a href="manage_exhibition_artworks.php">Assign Artwork</a>
    <a href="manage_ticket_types.php">Manage Tickets</a>
    <a href="generate_reports.php">Generate Reports</a>
    <!-- <a href="add_section.php">Add Sections</a> -->
    <a href="system_logs.php">View Logs</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</nav>

<main class="content">
    <h1 class="mb-4">Welcome, Admin <?= htmlspecialchars($_SESSION['user']['name']) ?></h1>
    <p>Select an option from the sidebar to begin managing the system.</p>

    <!-- Dashboard Summary Cards -->
    <div class="row dashboard-cards mb-4">
        <?php foreach ($ticketStats as $stat): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><?= htmlspecialchars($stat['title']) ?></h6>
                        <p class="card-text display-6"><?= (int)$stat['tickets_sold'] ?></p>
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
                    <p class="card-text fs-3"><?= (int)$artistCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-light mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Artworks</h5>
                    <p class="card-text fs-3"><?= (int)$artworkCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-light mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Exhibitions</h5>
                    <p class="card-text fs-3"><?= (int)$exhibitionCount ?></p>
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

<div class="bottom-bar">
    © 2025 AEMS. All rights reserved. Designed for admins to manage, explore and enjoy exhibitions.
</div>

</body>
</html>

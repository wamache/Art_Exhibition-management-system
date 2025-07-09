<?php
session_start();
include '../config/db.php';  // Your DB connection

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$userName = htmlspecialchars($_SESSION['user']['name']);

// Fetch upcoming exhibitions
$query = "SELECT id, title, start_date, end_date, description 
          FROM exhibitions 
          WHERE start_date >= CURDATE() 
          ORDER BY start_date ASC
          LIMIT 5";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$upcomingExhibitions = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $upcomingExhibitions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Visitor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
        }

        nav.sidebar {
            width: 220px;
            background-color: #2c3e50;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
        }

        nav.sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        nav.sidebar li {
            margin: 0;
        }

        nav.sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: background 0.3s;
            font-weight: 500;
        }

        nav.sidebar a:hover,
        nav.sidebar a.active {
            background-color: #2980b9;
            font-weight: 700;
        }

        main.main-content {
            margin-left: 220px;
            padding: 30px;
            flex: 1;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            nav.sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            main.main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<nav class="sidebar" aria-label="Sidebar navigation">
    <ul>
        <li><a href="profile.php" class="<?= ($current_page == 'profile.php') ? 'active' : '' ?>">Profile</a></li>
        <li><a href="view_exhibitions.php" class="<?= ($current_page == 'view_exhibitions.php') ? 'active' : '' ?>">View Exhibitions</a></li>
        <li><a href="explore_artists.php" class="<?= ($current_page == 'explore_artists.php') ? 'active' : '' ?>">Explore Artists</a></li>
        <li><a href="purchase_ticket.php" class="<?= ($current_page == 'purchase_ticket.php') ? 'active' : '' ?>">Purchase Tickets</a></li>
        <li><a href="give_feedback.php" class="<?= ($current_page == 'give_feedback.php') ? 'active' : '' ?>">Feedback</a></li>
        <li><a href="subscribe_updates.php" class="<?= ($current_page == 'subscribe_updates.php') ? 'active' : '' ?>">Register for Event Updates</a></li>
        <li><a href="../logout.php" style="color: #e74c3c;">Logout</a></li>
    </ul>
</nav>

<main class="main-content" role="main">
    <h1 class="mb-4">Welcome, <?= $userName ?>!</h1>
    <p>Select an option from the sidebar to begin exploring the art exhibition.</p>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Upcoming Exhibitions</h5>

            <?php if (count($upcomingExhibitions) === 0): ?>
                <p class="card-text">No upcoming exhibitions at the moment.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($upcomingExhibitions as $exhibition): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($exhibition['title']) ?></strong><br />
                            <small>
                                From <?= date('M d, Y', strtotime($exhibition['start_date'])) ?>
                                to <?= date('M d, Y', strtotime($exhibition['end_date'])) ?>
                            </small>
                            <p><?= nl2br(htmlspecialchars($exhibition['description'])) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Recent News & Updates</h5>
            <p class="card-text">Stay tuned for the latest announcements.</p>
        </div>
    </div>
    <div class="bottom-bar">   

© 2025 AEMS. All rights reserved.
Designed for visitors to explore and enjoy exhibitions.

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
    
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


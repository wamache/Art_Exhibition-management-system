<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visitor Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #2c3e50;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar li {
            margin: 10px 0;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .main-content {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <ul>
            <li><a href="view_exhibitions.php">View Exhibitions</a></li>
            <!-- <li><a href="view_artworks.php">View Artworks</a></li> -->
            <li><a href="explore_artists.php">Explore Artists</a></li>
            <li><a href="purchase_ticket.php">Purchase Tickets</a></li>
            <li><a href="subscribe_updates.php">Register for Event Updates</a></li>
            <!-- <li><a href="view_artist_artworks.php">View Artist Artwork</a></li> -->
        </ul>
    </div>

    <div class="main-content">
        <h1>Welcome, Visitor!</h1>
        <p>Select an option from the sidebar to begin exploring.</p>
    </div>

</body>
</html>

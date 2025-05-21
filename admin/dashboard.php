<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h1>Welcome, Admin <?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?></h1>

<ul>
    <li><a href="/project/art_exhibition/admin/add_users.php">Add Users</a></li>
    <li><a href="/project/art_exhibition/admin/add_artist.php">Add Artists</a></li>
    <li><a href="/project/art_exhibition/admin/manage_artwork.php">Manage Artworks</a></li>
    <li><a href="/project/art_exhibition/admin/manage_exhibitions.php">Manage Exhibitions</a></li>
    <li><a href="/project/art_exhibition/admin/manage_exhibition_artworks.php">Assign Artwork</a></li>
    <li><a href="/project/art_exhibition/admin/manage_ticket_types.php">Manage Tickets</a></li>
    <li><a href="/project/art_exhibition/admin/generate_reports.php">Generate Reports</a></li>
    <li><a href="/project/art_exhibition/admin/system_logs.php">View Logs</a></li>
    <li><a href="/project/art_exhibition/auth/logout.php">Logout</a></li>
</ul>
</body>
</html>

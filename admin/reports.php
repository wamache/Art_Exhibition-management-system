<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

// Exhibition attendance
$exhibition_sales = $conn->query("
    SELECT exhibitions.title, COUNT(sales.id) as tickets_sold
    FROM sales
    JOIN tickets ON sales.ticket_id = tickets.id
    JOIN exhibitions ON tickets.exhibition_id = exhibitions.id
    GROUP BY exhibitions.id
");

// Top performing artists
$artist_performance = $conn->query("
    SELECT users.name, COUNT(sales.id) AS total_sales
    FROM sales
    JOIN tickets ON sales.ticket_id = tickets.id
    JOIN exhibition_artworks ea ON tickets.exhibition_id = ea.exhibition_id
    JOIN artworks ON ea.artwork_id = artworks.id
    JOIN artists ON artworks.artist_id = artists.id
    JOIN users ON artists.user_id = users.id
    GROUP BY users.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Reports</h2>

    <h3>Exhibition Ticket Sales</h3>
    <table class="table table-bordered table-striped mb-5">
        <thead class="table-dark">
            <tr>
                <th>Exhibition</th>
                <th>Tickets Sold</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $exhibition_sales->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['tickets_sold'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Top Performing Artists (By Ticket Sales)</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Artist</th>
                <th>Sales</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $artist_performance->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['total_sales'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

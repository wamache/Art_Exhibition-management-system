<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <style>
        h2 { margin-top: 40px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Admin Reports</h1>

    <!-- 1. Ticket Sales by Exhibition -->
    <h2>Ticket Sales by Exhibition</h2>
    <?php
    $ticket_sales = $conn->query("
        SELECT e.title AS exhibition, COUNT(t.id) AS tickets_sold, SUM(t.price) AS total_sales
        FROM tickets t
        JOIN exhibitions e ON t.exhibition_id = e.id
        GROUP BY e.id
    ");

    if ($ticket_sales->num_rows > 0): ?>
        <table>
            <tr><th>Exhibition</th><th>Tickets Sold</th><th>Total Sales</th></tr>
            <?php while ($row = $ticket_sales->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['exhibition']) ?></td>
                    <td><?= $row['tickets_sold'] ?></td>
                    <td>$<?= number_format($row['total_sales'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: echo "<p>No ticket sales data available.</p>"; endif; ?>


    <!-- 2. Artist Performance (Number of Artworks in Exhibitions) -->
    <h2>Artist Performance</h2>
    <?php
    $artist_perf = $conn->query("
        SELECT u.name AS artist, COUNT(ea.artwork_id) AS total_artworks
        FROM users u
        JOIN artworks a ON u.id = a.artist_id
        JOIN exhibition_artworks ea ON a.id = ea.artwork_id
        WHERE u.role = 'artist'
        GROUP BY u.id
    ");

    if ($artist_perf->num_rows > 0): ?>
        <table>
            <tr><th>Artist</th><th>Total Artworks Displayed</th></tr>
            <?php while ($row = $artist_perf->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['artist']) ?></td>
                    <td><?= $row['total_artworks'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: echo "<p>No artist performance data available.</p>"; endif; ?>


    <!-- 3. Exhibition Attendance (Ticket count per Exhibition) -->
    <h2>Exhibition Attendance</h2>
    <?php
    $attendance = $conn->query("
        SELECT e.title AS exhibition, COUNT(t.id) AS attendees
        FROM tickets t
        JOIN exhibitions e ON t.exhibition_id = e.id
        GROUP BY e.id
    ");

    if ($attendance->num_rows > 0): ?>
        <table>
            <tr><th>Exhibition</th><th>Attendees</th></tr>
            <?php while ($row = $attendance->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['exhibition']) ?></td>
                    <td><?= $row['attendees'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: echo "<p>No exhibition attendance data available.</p>"; endif; ?>

</body>
</html>

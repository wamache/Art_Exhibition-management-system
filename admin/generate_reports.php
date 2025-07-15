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
<<<<<<< HEAD
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
        SELECT e.title AS exhibition, COUNT(t.id) AS tickets_sold, IFNULL(SUM(t.price), 0) AS total_sales
        FROM tickets t
        JOIN exhibitions e ON t.exhibition_id = e.id
=======
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f7f7f7;
        }
        h1 { text-align: center; }
        h2 { margin-top: 50px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>📊 Admin Reports</h1>

    <!-- 1. Ticket Sales by Exhibition -->
    <h2>🎟 Ticket Sales by Exhibition</h2>
    <?php
    $ticket_sales = $conn->query("
        SELECT 
            e.title AS exhibition,
            COUNT(t.id) AS tickets_sold,
            IFNULL(SUM(tt.price), 0) AS total_sales
        FROM tickets t
        JOIN exhibitions e ON t.exhibition_id = e.id
        JOIN ticket_types tt ON t.ticket_type = tt.type
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
        GROUP BY e.id
    ");

    if ($ticket_sales && $ticket_sales->num_rows > 0): ?>
        <table>
            <tr><th>Exhibition</th><th>Tickets Sold</th><th>Total Sales</th></tr>
            <?php while ($row = $ticket_sales->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['exhibition']) ?></td>
                    <td><?= (int)$row['tickets_sold'] ?></td>
                    <td>$<?= number_format((float)$row['total_sales'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: echo "<p>No ticket sales data available.</p>"; endif; ?>


<<<<<<< HEAD
    <!-- 2. Artist Performance (Number of Artworks in Exhibitions) -->
    <h2>Artist Performance</h2>
=======
    <!-- 2. Artist Performance -->
    <h2>🎨 Artist Performance</h2>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    <?php
    $artist_perf = $conn->query("
        SELECT u.name AS artist, COUNT(ea.artwork_id) AS total_artworks
        FROM users u
        JOIN artworks a ON u.id = a.artist_id
        JOIN exhibition_artworks ea ON a.id = ea.artwork_id
        WHERE u.role = 'artist'
        GROUP BY u.id
    ");

    if ($artist_perf && $artist_perf->num_rows > 0): ?>
        <table>
            <tr><th>Artist</th><th>Total Artworks Displayed</th></tr>
            <?php while ($row = $artist_perf->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['artist']) ?></td>
                    <td><?= (int)$row['total_artworks'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: echo "<p>No artist performance data available.</p>"; endif; ?>


<<<<<<< HEAD
    <!-- 3. Exhibition Attendance (Ticket count per Exhibition) -->
    <h2>Exhibition Attendance</h2>
=======
    <!-- 3. Exhibition Attendance -->
    <h2>👥 Exhibition Attendance</h2>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    <?php
    $attendance = $conn->query("
        SELECT e.title AS exhibition, COUNT(t.id) AS attendees
        FROM tickets t
        JOIN exhibitions e ON t.exhibition_id = e.id
        GROUP BY e.id
    ");

    if ($attendance && $attendance->num_rows > 0): ?>
        <table>
            <tr><th>Exhibition</th><th>Attendees</th></tr>
            <?php while ($row = $attendance->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['exhibition']) ?></td>
                    <td><?= (int)$row['attendees'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: echo "<p>No exhibition attendance data available.</p>"; endif; ?>
<<<<<<< HEAD

=======
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
</body>
</html>

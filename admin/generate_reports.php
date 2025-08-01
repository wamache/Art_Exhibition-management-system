<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Reports - Art Exhibition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
            background-size: cover;
            padding: 40px 20px;
            color: #212529;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px;
            max-width: 1000px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        p.no-data {
            text-align: center;
            font-style: italic;
            color: #6c757d;
            margin-bottom: 40px;
        }
        /* Back button styling */
        .btn-back {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 999;
        }
    </style>
</head>
<body>

    <a href="dashboard.php" class="btn btn-outline-dark btn-back">&larr; Back to Dashboard</a>

    <div class="container">
        <h1>🎨 Admin Reports</h1>

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
            LEFT JOIN ticket_types tt ON t.ticket_type = tt.type
            GROUP BY e.id
            ORDER BY total_sales DESC
        ");

        if ($ticket_sales && $ticket_sales->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Exhibition</th>
                        <th>Tickets Sold</th>
                        <th>Total Sales (ksh)</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $ticket_sales->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['exhibition']) ?></td>
                        <td><?= (int)$row['tickets_sold'] ?></td>
                        <td><?= number_format((float)$row['total_sales'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No ticket sales data available.</p>
        <?php endif; ?>


        <!-- 2. Artist Performance -->
        <h2>🎨 Artist Performance</h2>
        <?php
        $artist_perf = $conn->query("
            SELECT u.name AS artist, COUNT(ea.artwork_id) AS total_artworks
            FROM users u
            JOIN artworks a ON u.id = a.artist_id
            JOIN exhibition_artworks ea ON a.id = ea.artwork_id
            WHERE u.role = 'artist'
            GROUP BY u.id
            ORDER BY total_artworks DESC
        ");

        if ($artist_perf && $artist_perf->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Artist</th>
                        <th>Total Artworks Displayed</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $artist_perf->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['artist']) ?></td>
                        <td><?= (int)$row['total_artworks'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No artist performance data available.</p>
        <?php endif; ?>


        <!-- 3. Exhibition Attendance -->
        <h2>👥 Exhibition Attendance</h2>
        <?php
        $attendance = $conn->query("
            SELECT e.title AS exhibition, COUNT(t.id) AS attendees
            FROM tickets t
            JOIN exhibitions e ON t.exhibition_id = e.id
            GROUP BY e.id
            ORDER BY attendees DESC
        ");

        if ($attendance && $attendance->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Exhibition</th>
                        <th>Attendees</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $attendance->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['exhibition']) ?></td>
                        <td><?= (int)$row['attendees'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No exhibition attendance data available.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

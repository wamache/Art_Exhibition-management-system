<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];

// Prepared statement for security
$stmt = $conn->prepare("
    SELECT DISTINCT exhibitions.*
    FROM exhibitions
    JOIN exhibition_artworks ON exhibitions.id = exhibition_artworks.exhibition_id
    JOIN artworks ON exhibition_artworks.artwork_id = artworks.id
    WHERE artworks.artist_id = ?
");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$exhibitions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Exhibitions</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    body {
        background: 
            url('https://www.transparenttextures.com/patterns/diagmonds-light.png'), 
            linear-gradient(135deg, #e2e8f0, #cbd5e1);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
        margin-top: 60px;
        background: white;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.07);
        max-width: 900px;
    }
    h2 {
        color: #3b82f6; /* Blue-500 */
        font-weight: 700;
        margin-bottom: 30px;
        text-align: center;
    }
    .back-btn {
        margin-bottom: 25px;
    }
    table {
        font-size: 1rem;
    }
    th {
        background-color: #e0e7ff; /* Indigo-100 */
        font-weight: 600;
    }
    td {
        vertical-align: middle;
    }
</style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="btn btn-outline-secondary back-btn">← Back to Dashboard</a>
    <h2>🎭 My Exhibitions</h2>

    <?php if ($exhibitions->num_rows === 0): ?>
        <div class="alert alert-info text-center fs-5">
            You are not currently part of any exhibitions.
        </div>
    <?php else: ?>
        <table class="table table-bordered table-hover shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Venue</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($ex['title']) ?></td>
                        <td><?= htmlspecialchars(date('F j, Y', strtotime($ex['date']))) ?></td>
                        <td><?= htmlspecialchars($ex['venue']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$stmt->close();
?>

</body>
</html>

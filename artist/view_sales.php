<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    header("Location: /project/art_exhibition/artist/login.php");
    exit;
}

include '../config/db.php';

$artist_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("
    SELECT exhibitions.id, exhibitions.title AS exhibition, COUNT(sales.id) AS tickets_sold
    FROM sales
    JOIN tickets ON sales.ticket_id = tickets.id
    JOIN exhibitions ON tickets.exhibition_id = exhibitions.id
    JOIN exhibition_artworks ea ON exhibitions.id = ea.exhibition_id
    JOIN artworks ON ea.artwork_id = artworks.id
    WHERE artworks.artist_id = ?
    GROUP BY exhibitions.id, exhibitions.title
    ORDER BY exhibitions.title ASC
");

$stmt->bind_param("i", $artist_id);
$stmt->execute();
$sales = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Ticket Sales</title>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* Background with subtle pattern and gradient */
    body {
        background:
            url('https://www.transparenttextures.com/patterns/diagmonds-light.png'),
            linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
        padding-top: 70px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #222;
    }

    .container {
        max-width: 800px;
        background: rgba(255, 255, 255, 0.95);
        padding: 40px 50px;
        border-radius: 15px;
        box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        position: relative;
    }

    h2 {
        margin-bottom: 35px;
        text-align: center;
        font-weight: 700;
        color: #4b0082;
        letter-spacing: 1.1px;
    }

    table {
        font-size: 1rem;
    }

    .no-data {
        text-align: center;
        font-style: italic;
        color: #6c757d;
        padding: 20px 0;
    }

    /* Back button */
    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #4b0082;
        color: white;
        padding: 10px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 3px 8px rgba(75, 0, 130, 0.4);
        transition: background-color 0.3s ease;
    }
    .back-btn:hover {
        background-color: #6a26d0;
        text-decoration: none;
        color: white;
    }
</style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="back-btn">← Back</a>
    <h2>Ticket Sales for My Exhibitions</h2>

    <?php if ($sales->num_rows > 0): ?>
        <table id="salesTable" class="table table-striped table-bordered table-hover" style="display:none;">
            <thead class="table-dark">
                <tr>
                    <th>Exhibition</th>
                    <th>Tickets Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $sales->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['exhibition']) ?></td>
                        <td><?= (int)$row['tickets_sold'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No sales data found for your exhibitions.</p>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $("#salesTable").fadeIn(800);
    });
</script>

<!-- Bootstrap 5 JS Bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $stmt->close(); ?>

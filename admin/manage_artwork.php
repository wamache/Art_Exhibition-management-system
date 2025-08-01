<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
include '../config/db.php';

$artworks = $conn->query("SELECT artworks.*, users.name AS artist_name 
                          FROM artworks 
                          JOIN artists ON artworks.artist_id = artists.id
                          JOIN users ON artists.user_id = users.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Artworks</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Background with subtle pattern + gradient */
        body {
            min-height: 100vh;
            background: 
                linear-gradient(135deg, rgba(38, 70, 83, 0.8), rgba(42, 157, 143, 0.8)),
                url('https://www.transparenttextures.com/patterns/diagmonds-light.png') repeat;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f0f0f0;
            padding: 40px 20px;
        }

        /* Center container and add card styling */
        .container {
            max-width: 1000px;
            background: #114b5f;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
        }

        h2 {
            font-weight: 700;
            color: #d4f1f4;
            letter-spacing: 1.1px;
        }

        /* Top bar flex */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        /* Back button style */
        .btn-back {
            background: #3aafa9;
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            margin-right: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back:hover {
            background: #2b7a78;
            color: #e0fbfc;
            text-decoration: none;
        }

        /* Table styling */
        table {
            background: #3aafa9;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        thead tr {
            background-color: #17252a !important;
            color: #d4f1f4;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        tbody tr {
            background-color: #116466;
            transition: background-color 0.25s ease;
        }
        tbody tr:hover {
            background-color: #52b69a;
            color: #17252a;
            cursor: pointer;
        }

        tbody td, thead th {
            vertical-align: middle;
            padding: 12px 18px;
            border: none !important;
        }

        /* Buttons in table */
        .btn-sm {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 6px;
        }

        .btn-warning {
            background-color: #f4d35e;
            border: none;
            color: #17252a;
            transition: background-color 0.3s ease;
        }
        .btn-warning:hover {
            background-color: #e9c24b;
            color: #111;
        }

        .btn-danger {
            background-color: #ee6c4d;
            border: none;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #d65a3a;
        }

        .btn-primary {
            background-color: #3aafa9;
            border: none;
            font-weight: 700;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2b7a78;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div>
            <a href="dashboard.php" class="btn-back" title="Back to Dashboard">
                &#8592; Back
            </a>
        </div>
        <h2>Manage Artworks</h2>
        <a href="add_artwork.php" class="btn btn-primary">Add New Artwork</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Medium</th>
                    <th>Year</th>
                    <th>Artist</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $artworks->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['medium']) ?></td>
                    <td><?= $row['year_created'] ?></td>
                    <td><?= htmlspecialchars($row['artist_name']) ?></td>
                    <td class="text-center">
                        <a href="edit_artwork.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_artwork.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this artwork?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

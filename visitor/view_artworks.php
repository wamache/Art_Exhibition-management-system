<?php
session_start();
include '../config/db.php';

// Optional: Restrict access to only visitors
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

// Fetch artworks that are part of exhibitions
$query = "
    SELECT 
        artworks.title AS artwork_title,
        artworks.medium,
        artworks.year_created,
        artworks.description,
        artists.name AS artist_name,
        exhibitions.title AS exhibition_title,
        exhibitions.venue,
        exhibitions.start_date,
        exhibitions.end_date
    FROM artworks
    JOIN artists ON artworks.artist_id = artists.id
    JOIN exhibition_artworks ea ON artworks.id = ea.artwork_id
    JOIN exhibitions ON ea.exhibition_id = exhibitions.id
    ORDER BY exhibitions.start_date DESC, artworks.title ASC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Artworks</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #999; padding: 8px; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<h2>Explore Artworks in Exhibitions</h2>

<table>
    <tr>
        <th>Artwork Title</th>
        <th>Medium</th>
        <th>Year</th>
        <th>Description</th>
        <th>Artist</th>
        <th>Exhibition</th>
        <th>Venue</th>
        <th>Dates</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['artwork_title']) ?></td>
            <td><?= htmlspecialchars($row['medium']) ?></td>
            <td><?= htmlspecialchars($row['year_created']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td><?= htmlspecialchars($row['artist_name']) ?></td>
            <td><?= htmlspecialchars($row['exhibition_title']) ?></td>
            <td><?= htmlspecialchars($row['venue']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?> to <?= htmlspecialchars($row['end_date']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

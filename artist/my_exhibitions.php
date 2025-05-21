<?php
session_start();
include '../config/db.php';
$artist_id = $_SESSION['user']['id'];

$exhibitions = $conn->query("
    SELECT DISTINCT exhibitions.*
    FROM exhibitions
    JOIN exhibition_artworks ON exhibitions.id = exhibition_artworks.exhibition_id
    JOIN artworks ON exhibition_artworks.artwork_id = artworks.id
    WHERE artworks.artist_id = $artist_id
");
?>
<h2>My Exhibitions</h2>
<table border="1">
<tr><th>Title</th><th>Date</th><th>Venue</th></tr>
<?php while ($ex = $exhibitions->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($ex['title']) ?></td>
    <td><?= $ex['date'] ?></td>
    <td><?= htmlspecialchars($ex['venue']) ?></td>
</tr>
<?php endwhile; ?>
</table>

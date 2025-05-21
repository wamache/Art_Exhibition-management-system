<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM artworks WHERE artist_id = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<h2>My Artworks</h2>
<a href="add_artwork.php">Add New Artwork</a>

<!-- Display Message if Artworks Deleted -->
<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
    <p style="color:green;">Artwork deleted successfully!</p>
<?php endif; ?>

<table border="1">
    <tr>
        <th>Title</th>
        <th>Year</th>
        <th>Actions</th>
    </tr>
    <?php while ($art = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($art['title']) ?></td>
            <td><?= $art['year_created'] ?></td>
            <td>
                <a href="edit_artwork.php?id=<?= $art['id'] ?>">Edit</a> |
                <a href="delete_artwork.php?id=<?= $art['id'] ?>" onclick="return confirm('Are you sure you want to delete this artwork?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
$stmt->close();
?>

<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");

include '../config/db.php';

// Get list of exhibitions
$exhibitions = $conn->query("SELECT * FROM exhibitions");

// Handle selected exhibition
$selected_exhibition_id = isset($_GET['exhibition_id']) ? (int)$_GET['exhibition_id'] : 0;

$exhibition_title = '';
if ($selected_exhibition_id) {
    $stmt = $conn->prepare("SELECT title FROM exhibitions WHERE id = ?");
    $stmt->bind_param("i", $selected_exhibition_id);
    $stmt->execute();
    $stmt->bind_result($exhibition_title);
    $stmt->fetch();
    $stmt->close();
}

// Fetch assigned artworks
$assigned_artworks = [];
if ($selected_exhibition_id) {
    $query = "
        SELECT a.id, a.title, a.medium, a.year_created, u.name AS artist_name
        FROM exhibition_artworks ea
        JOIN artworks a ON ea.artwork_id = a.id
        JOIN users u ON a.artist_id = u.id
        WHERE ea.exhibition_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $selected_exhibition_id);
    $stmt->execute();
    $assigned_artworks = $stmt->get_result();
}

// Fetch all artworks (to assign new ones)
$all_artworks = $conn->query("SELECT a.id, a.title, u.name AS artist_name FROM artworks a JOIN users u ON a.artist_id = u.id");
?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
    <p style="color:red;">This artwork is already assigned to the exhibition.</p>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <p style="color:green;">Artwork successfully assigned!</p>
<?php endif; ?>



<h2>Manage Artworks in Exhibitions</h2>

<form method="get" action="">
    <label>Select Exhibition:</label>
    <select name="exhibition_id" onchange="this.form.submit()" required>
        <option value="">-- Choose --</option>
        <?php while ($ex = $exhibitions->fetch_assoc()): ?>
            <option value="<?= $ex['id'] ?>" <?= ($selected_exhibition_id == $ex['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($ex['title']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<?php if ($selected_exhibition_id): ?>
    <h3>Artworks in: <?= htmlspecialchars($exhibition_title) ?></h3>

    <table border="1" cellpadding="8">
        <tr>
            <th>Title</th>
            <th>Medium</th>
            <th>Year</th>
            <th>Artist</th>
            <th>Action</th>
        </tr>
        <?php while ($art = $assigned_artworks->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($art['title']) ?></td>
                <td><?= htmlspecialchars($art['medium']) ?></td>
                <td><?= $art['year_created'] ?></td>
                <td><?= htmlspecialchars($art['artist_name']) ?></td>
                <td><a href="remove_artwork_from_exhibition.php?exhibition_id=<?= $selected_exhibition_id ?>&artwork_id=<?= $art['id'] ?>" onclick="return confirm('Remove this artwork?')">Remove</a></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h3>Add Artwork to Exhibition</h3>
    <a href="assign_artworks.php">Add Artwork to Exhibition</a>
    <form method="post" action="add_artwork_to_exhibition.php">
        <input type="hidden" name="exhibition_id" value="<?= $selected_exhibition_id ?>">
        <label>Select Artwork:</label>
        <select name="artwork_id" required>
            <?php while ($art = $all_artworks->fetch_assoc()): ?>
                <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['title']) ?> (<?= htmlspecialchars($art['artist_name']) ?>)</option>
            <?php endwhile; ?>
        </select>
        <input type="submit" value="Assign to Exhibition">
    </form>
<?php endif; ?>

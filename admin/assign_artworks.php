<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Success or error message
if (isset($_GET['success'])) {
    echo "<p style='color: green;'>Artworks successfully assigned.</p>";
}
if (isset($_GET['error']) && $_GET['error'] === 'exists') {
    echo "<p style='color: red;'>One or more artworks already exist in the exhibition.</p>";
}

// Get all exhibitions
$exhibitions = $conn->query("SELECT * FROM exhibitions");

$exhibition_id = isset($_GET['exhibition_id']) ? intval($_GET['exhibition_id']) : null;

if ($exhibition_id) {
    // Handle form submission to assign artworks
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selected_artworks = $_POST['artworks'] ?? [];

        // Remove previous assignments
        $stmt = $conn->prepare("DELETE FROM exhibition_artworks WHERE exhibition_id = ?");
        $stmt->bind_param("i", $exhibition_id);
        $stmt->execute();
        $stmt->close();

        // Insert new assignments
        if (!empty($selected_artworks)) {
            $stmt = $conn->prepare("INSERT INTO exhibition_artworks (exhibition_id, artwork_id) VALUES (?, ?)");
            foreach ($selected_artworks as $artwork_id) {
                $artwork_id = intval($artwork_id);
                $stmt->bind_param("ii", $exhibition_id, $artwork_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Redirect to avoid resubmission and show message
        header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&success=1");
        exit;
    }

    // Fetch all artworks
    $artworks = $conn->query("SELECT * FROM artworks");

    // Get already assigned artwork IDs
    $assigned = $conn->prepare("SELECT artwork_id FROM exhibition_artworks WHERE exhibition_id = ?");
    $assigned->bind_param("i", $exhibition_id);
    $assigned->execute();
    $result = $assigned->get_result();

    $assigned_ids = [];
    while ($row = $result->fetch_assoc()) {
        $assigned_ids[] = $row['artwork_id'];
    }
    $assigned->close();

    // Get exhibition title
    $exhibition_title_stmt = $conn->prepare("SELECT title FROM exhibitions WHERE id = ?");
    $exhibition_title_stmt->bind_param("i", $exhibition_id);
    $exhibition_title_stmt->execute();
    $exhibition_title_stmt->bind_result($exhibition_title);
    $exhibition_title_stmt->fetch();
    $exhibition_title_stmt->close();
}
?>

<h2>Assign Artworks to Exhibition</h2>

<!-- Exhibition Selector -->
<form method="get">
    <label for="exhibition_id">Select Exhibition:</label>
    <select name="exhibition_id" id="exhibition_id" onchange="this.form.submit()">
        <option value="">-- Choose --</option>
        <?php while ($ex = $exhibitions->fetch_assoc()): ?>
            <option value="<?= $ex['id'] ?>" <?= $exhibition_id == $ex['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($ex['title']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<!-- Artwork Assignment Form -->
<?php if ($exhibition_id): ?>
    <h3>Assign Artworks to: <?= htmlspecialchars($exhibition_title) ?></h3>
    <form method="post">
        <?php foreach ($artworks as $art): ?>
            <label>
                <input type="checkbox" name="artworks[]" value="<?= $art['id'] ?>"
                    <?= in_array($art['id'], $assigned_ids) ? 'checked' : '' ?>>
                <?= htmlspecialchars($art['title']) ?>
            </label><br>
        <?php endforeach; ?>
        <br>
        <input type="submit" value="Assign Selected Artworks">
    </form>
<?php endif; ?>

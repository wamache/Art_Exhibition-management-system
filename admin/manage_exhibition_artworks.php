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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Artworks</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
        }
    </style>
</head>
<body>
<div class="container">

    <h2 class="mb-4">Manage Artworks in Exhibitions</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
        <div class="alert alert-danger">This artwork is already assigned to the exhibition.</div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Artwork successfully assigned!</div>
    <?php endif; ?>

    <form method="get" class="mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="exhibition_id" class="col-form-label">Select Exhibition:</label>
            </div>
            <div class="col-auto">
                <select name="exhibition_id" id="exhibition_id" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Choose --</option>
                    <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                        <option value="<?= $ex['id'] ?>" <?= ($selected_exhibition_id == $ex['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ex['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </form>

    <?php if ($selected_exhibition_id): ?>
        <h4 class="mb-3">Artworks in: <?= htmlspecialchars($exhibition_title) ?></h4>

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Medium</th>
                        <th>Year</th>
                        <th>Artist</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($art = $assigned_artworks->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($art['title']) ?></td>
                            <td><?= htmlspecialchars($art['medium']) ?></td>
                            <td><?= $art['year_created'] ?></td>
                            <td><?= htmlspecialchars($art['artist_name']) ?></td>
                            <td>
                                <a href="remove_artwork_from_exhibition.php?exhibition_id=<?= $selected_exhibition_id ?>&artwork_id=<?= $art['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Remove this artwork?')">Remove</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h4 class="mb-3">Add Artwork to Exhibition</h4>
        <form method="post" action="add_artwork_to_exhibition.php" class="row g-3 align-items-center">
            <input type="hidden" name="exhibition_id" value="<?= $selected_exhibition_id ?>">
            <div class="col-auto">
                <label for="artwork_id" class="col-form-label">Select Artwork:</label>
            </div>
            <div class="col-auto">
                <select name="artwork_id" id="artwork_id" class="form-select" required>
                    <?php while ($art = $all_artworks->fetch_assoc()): ?>
                        <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['title']) ?> (<?= htmlspecialchars($art['artist_name']) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Assign to Exhibition</button>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

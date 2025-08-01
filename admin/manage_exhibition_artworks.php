<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') die("Access denied");

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
    <meta charset="UTF-8" />
    <title>Manage Artworks in Exhibitions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2, #6a11cb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            padding-top: 70px; /* space for fixed back button */
            color: #333;
        }
        .container {
            max-width: 960px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            margin: auto;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 10000;
            font-weight: 600;
            font-size: 16px;
            color: #000;
            background: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .back-btn:hover {
            background: #f0f0f0;
            color: #333;
        }
        table th, table td {
            vertical-align: middle !important;
        }
        @media (max-width: 575.98px) {
            .container {
                padding: 20px 20px;
            }
        }
    </style>
</head>
<body>

<a href="manage_exhibitions.php" class="back-btn" title="Back to Manage Exhibitions">
    &larr; Back
</a>

<div class="container">
    <h2 class="mb-4 text-center">Manage Artworks in Exhibitions</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
        <div class="alert alert-danger">This artwork is already assigned to the exhibition.</div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Artwork successfully assigned!</div>
    <?php endif; ?>

    <form method="get" class="mb-4">
        <div class="row g-3 align-items-center justify-content-center">
            <div class="col-auto">
                <label for="exhibition_id" class="col-form-label fw-semibold">Select Exhibition:</label>
            </div>
            <div class="col-auto" style="min-width: 250px;">
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
        <h4 class="mb-3">Artworks in: <span class="text-primary"><?= htmlspecialchars($exhibition_title) ?></span></h4>

        <div class="table-responsive mb-4 shadow-sm rounded">
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
                    <?php if ($assigned_artworks->num_rows > 0): ?>
                        <?php while ($art = $assigned_artworks->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($art['title']) ?></td>
                                <td><?= htmlspecialchars($art['medium']) ?></td>
                                <td><?= $art['year_created'] ?></td>
                                <td><?= htmlspecialchars($art['artist_name']) ?></td>
                                <td>
                                    <a href="remove_artwork_from_exhibition.php?exhibition_id=<?= $selected_exhibition_id ?>&artwork_id=<?= $art['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Remove this artwork?')"
                                       title="Remove Artwork">
                                        Remove
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted fst-italic">No artworks assigned yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h4 class="mb-3">Add Artwork to Exhibition</h4>
        <form method="post" action="add_artwork_to_exhibition.php" class="row g-3 align-items-center justify-content-center">
            <input type="hidden" name="exhibition_id" value="<?= $selected_exhibition_id ?>">
            <div class="col-auto">
                <label for="artwork_id" class="col-form-label fw-semibold">Select Artwork:</label>
            </div>
            <div class="col-auto" style="min-width: 300px;">
                <select name="artwork_id" id="artwork_id" class="form-select" required>
                    <?php while ($art = $all_artworks->fetch_assoc()): ?>
                        <option value="<?= $art['id'] ?>">
                            <?= htmlspecialchars($art['title']) ?> (<?= htmlspecialchars($art['artist_name']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-4">Assign to Exhibition</button>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

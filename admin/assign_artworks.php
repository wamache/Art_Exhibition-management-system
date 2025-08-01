<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$successMsg = '';
$errorMsg = '';

if (isset($_GET['success'])) {
    $successMsg = "Artworks successfully assigned.";
}
if (isset($_GET['error']) && $_GET['error'] === 'exists') {
    $errorMsg = "One or more artworks already exist in the exhibition.";
}

$exhibitions = $conn->query("SELECT * FROM exhibitions ORDER BY title");

$exhibition_id = isset($_GET['exhibition_id']) ? intval($_GET['exhibition_id']) : null;

if ($exhibition_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Invalid CSRF token");
        }

        $selected_artworks = $_POST['artworks'] ?? [];

        $stmt = $conn->prepare("DELETE FROM exhibition_artworks WHERE exhibition_id = ?");
        $stmt->bind_param("i", $exhibition_id);
        $stmt->execute();
        $stmt->close();

        if (!empty($selected_artworks)) {
            $stmt = $conn->prepare("INSERT INTO exhibition_artworks (exhibition_id, artwork_id) VALUES (?, ?)");
            foreach ($selected_artworks as $artwork_id) {
                $artwork_id = intval($artwork_id);
                $stmt->bind_param("ii", $exhibition_id, $artwork_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        header("Location: assign_artworks_to_exhibition.php?exhibition_id=$exhibition_id&success=1");
        exit;
    }

    // Fetch artworks
    $artworks_stmt = $conn->prepare("SELECT id, title FROM artworks ORDER BY title");
    $artworks_stmt->execute();
    $artworks_result = $artworks_stmt->get_result();

    $assigned_stmt = $conn->prepare("SELECT artwork_id FROM exhibition_artworks WHERE exhibition_id = ?");
    $assigned_stmt->bind_param("i", $exhibition_id);
    $assigned_stmt->execute();
    $result = $assigned_stmt->get_result();

    $assigned_ids = [];
    while ($row = $result->fetch_assoc()) {
        $assigned_ids[] = $row['artwork_id'];
    }
    $assigned_stmt->close();

    $title_stmt = $conn->prepare("SELECT title FROM exhibitions WHERE id = ?");
    $title_stmt->bind_param("i", $exhibition_id);
    $title_stmt->execute();
    $title_stmt->bind_result($exhibition_title);
    $title_stmt->fetch();
    $title_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Assign Artworks to Exhibition</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2, #6a11cb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            padding-top: 70px; /* space for fixed back button */
        }
        .container {
            max-width: 900px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            margin: auto;
            position: relative;
        }
        .artwork-checkbox {
            display: block;
            margin-bottom: 8px;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 9999;
            font-weight: 600;
            font-size: 16px;
            color: #000;
            background: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .back-btn:hover {
            background: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>

<a href="manage_exhibitions.php" class="back-btn">
    &larr; Back
</a>

<div class="container">
    <h2 class="mb-4 text-center">Assign Artworks to Exhibition</h2>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="get" class="mb-4">
        <div class="mb-3">
            <label for="exhibition_id" class="form-label">Select Exhibition:</label>
            <select name="exhibition_id" id="exhibition_id" class="form-select" onchange="this.form.submit()" required>
                <option value="">-- Choose Exhibition --</option>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <option value="<?= $ex['id'] ?>" <?= $exhibition_id == $ex['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ex['title']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <?php if ($exhibition_id): ?>
        <h4 class="mb-3">Assign Artworks to: <strong><?= htmlspecialchars($exhibition_title) ?></strong></h4>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />

            <div class="mb-2 form-check">
                <input type="checkbox" id="select_all" class="form-check-input me-1" />
                <label for="select_all" class="form-check-label">Select / Deselect All</label>
            </div>

            <div class="mb-3" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
                <?php while ($art = $artworks_result->fetch_assoc()): ?>
                    <div class="form-check artwork-checkbox">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="artwork<?= $art['id'] ?>"
                            name="artworks[]"
                            value="<?= $art['id'] ?>"
                            <?= in_array($art['id'], $assigned_ids) ? 'checked' : '' ?>
                        />
                        <label class="form-check-label" for="artwork<?= $art['id'] ?>">
                            <?= htmlspecialchars($art['title']) ?>
                        </label>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="submit" class="btn btn-primary mt-3 w-100">Assign Selected Artworks</button>
        </form>
    <?php endif; ?>
</div>

<script>
    document.getElementById('select_all')?.addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('input[name="artworks[]"]').forEach(cb => cb.checked = checked);
    });
</script>

</body>
</html>

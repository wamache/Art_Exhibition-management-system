<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') die("Access denied");

include '../config/db.php';

// Validate and sanitize ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid artwork ID.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $medium = trim($_POST['medium']);
    $year = trim($_POST['year']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE artworks SET title = ?, medium = ?, year_created = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $medium, $year, $description, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_artworks.php");
    exit;
}

// Fetch artwork securely
$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$artwork = $result->fetch_assoc();
$stmt->close();

if (!$artwork) {
    die("Artwork not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Artwork</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            padding: 30px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 25px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Artwork</h2>
    <form method="post" id="editArtworkForm" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($artwork['title']) ?>" required />
            <div class="invalid-feedback">Please enter the artwork title.</div>
        </div>

        <div class="mb-3">
            <label for="medium" class="form-label">Medium</label>
            <input type="text" class="form-control" id="medium" name="medium" value="<?= htmlspecialchars($artwork['medium']) ?>" />
        </div>

        <div class="mb-3">
            <label for="year" class="form-label">Year Created</label>
            <input type="text" class="form-control" id="year" name="year" value="<?= htmlspecialchars($artwork['year_created']) ?>" placeholder="YYYY" pattern="^\d{4}$" />
            <div class="invalid-feedback">Please enter a valid 4-digit year or leave blank.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($artwork['description']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Artwork</button>
    </form>
</div>

<script>
    (function() {
        'use strict';
        const form = document.getElementById('editArtworkForm');

        form.addEventListener('submit', function(event) {
            // Validate year format or allow empty
            const yearInput = form.year;
            const yearValue = yearInput.value.trim();
            const yearPattern = /^\d{4}$/;

            if (yearValue !== '' && !yearPattern.test(yearValue)) {
                yearInput.setCustomValidity("Invalid");
            } else {
                yearInput.setCustomValidity("");
            }

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    })();
</script>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist_id = $_POST['artist_id'];
    $title = $_POST['title'];
    $medium = $_POST['medium'];
    $year = $_POST['year'];
    $description = $_POST['description'];

    if ($year !== '' && !preg_match('/^\d{4}$/', $year)) {
        die("Invalid year format. Please enter a 4-digit year or leave empty.");
    }

    $stmt = $conn->prepare("INSERT INTO artworks (artist_id, title, medium, year_created, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $artist_id, $title, $medium, $year, $description);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user']['id'], 'Added new artwork', "Artwork: $title, Artist ID: $artist_id");
        header("Location: manage_artwork.php?success=1");
        exit;
    } else {
        die("Error inserting artwork: " . $stmt->error);
    }
}

$artists = $conn->query("SELECT artists.id, users.name FROM artists JOIN users ON artists.user_id = users.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Artwork</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: #f8f9fa;
            padding: 30px;
        }
        h2 {
            margin-bottom: 20px;
        }
        form {
            max-width: 600px;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Artwork</h2>
    <form method="post" id="addArtworkForm" novalidate>
        <div class="mb-3">
            <label for="artist_id" class="form-label">Artist</label>
            <select name="artist_id" id="artist_id" class="form-select" required>
                <option value="" disabled selected>Select an artist</option>
                <?php while ($a = $artists->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Please select an artist.</div>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" required />
            <div class="invalid-feedback">Title is required.</div>
        </div>

        <div class="mb-3">
            <label for="medium" class="form-label">Medium</label>
            <input type="text" name="medium" id="medium" class="form-control" />
        </div>

        <div class="mb-3">
            <label for="year" class="form-label">Year Created</label>
            <input type="text" name="year" id="year" class="form-control" placeholder="YYYY" pattern="^\d{4}$" />
            <div class="invalid-feedback">Please enter a valid 4-digit year or leave empty.</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Add Artwork</button>
    </form>
</div>

<script>
    // Bootstrap validation example
    (function () {
        'use strict'
        const form = document.getElementById('addArtworkForm')

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            // Custom year validation (optional)
            const yearInput = document.getElementById('year').value.trim()
            if (yearInput !== '' && !/^\d{4}$/.test(yearInput)) {
                event.preventDefault()
                event.stopPropagation()
                $('#year').addClass('is-invalid')
            } else {
                $('#year').removeClass('is-invalid')
            }

            form.classList.add('was-validated')
        }, false)
    })()
</script>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

// Sanitize and validate ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid exhibition ID.");
}

// Fetch exhibition
$stmt = $conn->prepare("SELECT * FROM exhibitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exhibition = $result->fetch_assoc();
$stmt->close();

if (!$exhibition) {
    die("Exhibition not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $date = $_POST['date'];
    $venue = trim($_POST['venue']);

    // Optional: Add date validation here if desired

    $stmt = $conn->prepare("UPDATE exhibitions SET title = ?, date = ?, venue = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $date, $venue, $id);
    $stmt->execute();
    $stmt->close();

    // Optionally log the update
    // log_action($conn, $_SESSION['user']['id'], 'Updated Exhibition', "ID: $id");

    header("Location: manage_exhibitions.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Exhibition</title>
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
    <h2>Edit Exhibition</h2>
    <form method="post" id="editExhibitionForm" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input
                type="text"
                class="form-control"
                id="title"
                name="title"
                value="<?= htmlspecialchars($exhibition['title']) ?>"
                required
            />
            <div class="invalid-feedback">Please enter the exhibition title.</div>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
            <input
                type="date"
                class="form-control"
                id="date"
                name="date"
                value="<?= htmlspecialchars($exhibition['date']) ?>"
                required
            />
            <div class="invalid-feedback">Please select a valid date.</div>
        </div>

        <div class="mb-3">
            <label for="venue" class="form-label">Venue <span class="text-danger">*</span></label>
            <input
                type="text"
                class="form-control"
                id="venue"
                name="venue"
                value="<?= htmlspecialchars($exhibition['venue']) ?>"
                required
            />
            <div class="invalid-feedback">Please enter the venue.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Exhibition</button>
    </form>
</div>

<script>
    (function () {
        'use strict';
        const form = document.getElementById('editExhibitionForm');

        form.addEventListener('submit', function (event) {
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

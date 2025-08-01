<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid exhibition ID.");
}

$stmt = $conn->prepare("SELECT * FROM exhibitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exhibition = $result->fetch_assoc();
$stmt->close();

if (!$exhibition) {
    die("Exhibition not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = trim($_POST['location']);

    $stmt = $conn->prepare("UPDATE exhibitions SET title = ?, start_date = ?, end_date = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $start_date, $end_date, $location, $id);
    $stmt->execute();
    $stmt->close();

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 40px 15px;
            color: #fff;
        }
        .form-container {
            max-width: 650px;
            margin: auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            color: #212529;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            color: #343a40;
        }
        .btn-primary {
            background-color: #4e3bbf;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #3829a3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
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
            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
            <input
                type="date"
                class="form-control"
                id="start_date"
                name="start_date"
                value="<?= htmlspecialchars($exhibition['start_date']) ?>"
                required
            />
            <div class="invalid-feedback">Please select a start date.</div>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
            <input
                type="date"
                class="form-control"
                id="end_date"
                name="end_date"
                value="<?= htmlspecialchars($exhibition['end_date']) ?>"
                required
            />
            <div class="invalid-feedback">Please select an end date.</div>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <input
                type="text"
                class="form-control"
                id="location"
                name="location"
                value="<?= htmlspecialchars($exhibition['location']) ?>"
                required
            />
            <div class="invalid-feedback">Please enter the location.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Exhibition</button>
        <a href="manage_exhibitions.php" class="btn btn-secondary w-100 mt-3">&larr; Back</a>
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

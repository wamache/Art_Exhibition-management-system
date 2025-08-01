<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Log action function
function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = trim($_POST['location']);

    if (empty($title) || empty($start_date) || empty($end_date) || empty($location)) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("INSERT INTO exhibitions (title, start_date, end_date, location) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $start_date, $end_date, $location);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user']['id'], 'Created new exhibition', "Title: $title, Dates: $start_date to $end_date, Location: $location");
        header("Location: manage_exhibitions.php?success=1");
        exit;
    } else {
        die("Error creating exhibition: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Exhibition</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 15px;
            color: #fff;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            color: #212529;
        }
        h2 {
            margin-bottom: 25px;
            text-align: center;
            color: #333;
            font-weight: 700;
        }
        .btn-primary {
            background-color: #5a47ab;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #3f2f82;
        }
        .btn-back {
            background-color: #ffffff;
            color: #333;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .btn-back:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>

<div class="form-container">
    <a href="manage_exhibitions.php" class="btn btn-back">&larr; Back to Exhibitions</a>

    <h2>Add New Exhibition</h2>
    <form method="post" id="addExhibitionForm" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" required />
            <div class="invalid-feedback">Please enter the exhibition title.</div>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
            <input type="date" name="start_date" id="start_date" class="form-control" required />
            <div class="invalid-feedback">Please select a start date.</div>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
            <input type="date" name="end_date" id="end_date" class="form-control" required />
            <div class="invalid-feedback">Please select an end date.</div>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <input type="text" name="location" id="location" class="form-control" required />
            <div class="invalid-feedback">Please enter the exhibition location.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Create Exhibition</button>
    </form>
</div>

<script>
    (function () {
        'use strict';
        const form = document.getElementById('addExhibitionForm');
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

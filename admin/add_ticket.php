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
    $type = $_POST['type'];
    $price = $_POST['price'];
    $exhibition_id = $_POST['exhibition_id'];

    // Basic price validation
    if (!is_numeric($price) || $price < 0) {
        die("Invalid price.");
    }

    $stmt = $conn->prepare("INSERT INTO ticket_types (type, price, exhibition_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $type, $price, $exhibition_id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user']['id'], 'Created ticket type', "Type: $type, Price: $price, Exhibition ID: $exhibition_id");
        header("Location: manage_ticket_types.php?success=1");
        exit;
    } else {
        die("Error creating ticket type: " . $stmt->error);
    }
}

$exhibitions = $conn->query("SELECT * FROM exhibitions");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Ticket Type</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: #f8f9fa;
            padding: 30px;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Add New Ticket Type</h2>
    <form method="post" id="ticketTypeForm" novalidate>
        <div class="mb-3">
            <label for="type" class="form-label">Ticket Type <span class="text-danger">*</span></label>
            <select class="form-select" name="type" id="type" required>
                <option value="">Select ticket type</option>
                <option value="Standard">Standard</option>
                <option value="VIP">VIP</option>
            </select>
            <div class="invalid-feedback">Please select a ticket type.</div>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" class="form-control" name="price" id="price" required />
            <div class="invalid-feedback">Please enter a valid price.</div>
        </div>

        <div class="mb-3">
            <label for="exhibition_id" class="form-label">Exhibition <span class="text-danger">*</span></label>
            <select class="form-select" name="exhibition_id" id="exhibition_id" required>
                <option value="">Select exhibition</option>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['title']) ?></option>
                <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Please select an exhibition.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Create Ticket Type</button>
    </form>
</div>

<script>
    (function() {
        'use strict'
        const form = document.getElementById('ticketTypeForm');
        form.addEventListener('submit', function(event) {
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

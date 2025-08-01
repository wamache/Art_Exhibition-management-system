<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Validate ticket_type ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid ticket type ID.");
}

// Fetch current ticket type
$stmt = $conn->prepare("SELECT * FROM ticket_types WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$ticket_type = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket_type) {
    die("Ticket type not found.");
}

// Fetch exhibitions
$exhibitions = $conn->query("SELECT id, title FROM exhibitions ORDER BY title");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $price = $_POST['price'] ?? '';
    $exhibition_id = $_POST['exhibition_id'] ?? '';

    if (!in_array($type, ['Standard', 'VIP']) || !is_numeric($price) || $price < 0 || !is_numeric($exhibition_id)) {
        die("Invalid form input.");
    }

    $stmt = $conn->prepare("UPDATE ticket_types SET type = ?, price = ?, exhibition_id = ? WHERE id = ?");
    $stmt->bind_param("sdii", $type, $price, $exhibition_id, $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user']['id'], 'Updated ticket type', "ID: $id, Type: $type, Price: $price, Exhibition ID: $exhibition_id");
        header("Location: manage_ticket_types.php?updated=1");
        exit;
    } else {
        die("Error updating ticket type: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Ticket Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1549924231-f129b911e442?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            max-width: 600px;
            margin: 80px auto;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
        }

        .footer-note {
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<a href="manage_ticket_types.php" class="btn btn-outline-light position-absolute btn-back">&larr; Back</a>

<div class="form-container">
    <h2>Edit Ticket Type</h2>
    <form method="post" id="ticketTypeForm" novalidate>
        <div class="mb-3">
            <label for="type" class="form-label">Ticket Type <span class="text-danger">*</span></label>
            <select class="form-select" name="type" id="type" required>
                <option value="">Select ticket type</option>
                <option value="Standard" <?= $ticket_type['type'] === 'Standard' ? 'selected' : '' ?>>Standard</option>
                <option value="VIP" <?= $ticket_type['type'] === 'VIP' ? 'selected' : '' ?>>VIP</option>
            </select>
            <div class="invalid-feedback">Please select a ticket type.</div>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (Ksh) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" class="form-control" name="price" id="price" value="<?= htmlspecialchars($ticket_type['price']) ?>" required />
            <div class="invalid-feedback">Please enter a valid price.</div>
        </div>

        <div class="mb-3">
            <label for="exhibition_id" class="form-label">Exhibition <span class="text-danger">*</span></label>
            <select class="form-select" name="exhibition_id" id="exhibition_id" required>
                <option value="">Select exhibition</option>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <option value="<?= $ex['id'] ?>" <?= $ex['id'] == $ticket_type['exhibition_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ex['title']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Please select an exhibition.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Ticket Type</button>
    </form>

    <div class="footer-note">
        &copy; <?= date('Y') ?> AEMS. All rights reserved.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        'use strict';
        const form = document.getElementById('ticketTypeForm');
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    })();
</script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Sanitize and validate ticket ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid ticket ID.");
}

// Fetch ticket info
$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Ticket not found.");
}

// Fetch exhibitions for dropdown
$exhibitions = $conn->query("SELECT id, title FROM exhibitions ORDER BY title");

// Fetch ticket types for dropdown (only for this exhibition)
$ticket_types = [];
if ($ticket['exhibition_id']) {
    $stmt = $conn->prepare("SELECT id, type FROM ticket_types WHERE exhibition_id = ? ORDER BY type");
    $stmt->bind_param("i", $ticket['exhibition_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $ticket_types[] = $row;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exhibition_id = intval($_POST['exhibition_id']);
    $ticket_type_id = intval($_POST['ticket_type_id']);
    $buyer_name = trim($_POST['buyer_name']);
    $price = floatval($_POST['price']);

    // Basic validations
    if ($exhibition_id <= 0 || $ticket_type_id <= 0 || empty($buyer_name) || $price < 0) {
        die("Invalid form data.");
    }

    $stmt = $conn->prepare("UPDATE tickets SET exhibition_id = ?, ticket_type_id = ?, buyer_name = ?, price = ? WHERE id = ?");
    $stmt->bind_param("iisdi", $exhibition_id, $ticket_type_id, $buyer_name, $price, $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_tickets.php?success=1");
        exit;
    } else {
        die("Error updating ticket: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Ticket</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
  body {
    background: #f8f9fa;
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
  <h2>Edit Ticket</h2>
  <form id="editTicketForm" method="post" novalidate>
    <div class="mb-3">
      <label for="exhibition_id" class="form-label">Exhibition <span class="text-danger">*</span></label>
      <select id="exhibition_id" name="exhibition_id" class="form-select" required>
        <option value="" disabled>Select Exhibition</option>
        <?php while ($ex = $exhibitions->fetch_assoc()): ?>
          <option value="<?= $ex['id'] ?>" <?= $ex['id'] == $ticket['exhibition_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($ex['title']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <div class="invalid-feedback">Please select an exhibition.</div>
    </div>

    <div class="mb-3">
      <label for="ticket_type_id" class="form-label">Ticket Type <span class="text-danger">*</span></label>
      <select id="ticket_type_id" name="ticket_type_id" class="form-select" required>
        <option value="" disabled>Select Ticket Type</option>
        <?php foreach ($ticket_types as $tt): ?>
          <option value="<?= $tt['id'] ?>" <?= $tt['id'] == $ticket['ticket_type_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($tt['type']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback">Please select a ticket type.</div>
    </div>

    <div class="mb-3">
      <label for="buyer_name" class="form-label">Buyer Name <span class="text-danger">*</span></label>
      <input type="text" id="buyer_name" name="buyer_name" class="form-control" value="<?= htmlspecialchars($ticket['buyer_name']) ?>" required>
      <div class="invalid-feedback">Please enter the buyer's name.</div>
    </div>

    <div class="mb-3">
      <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
      <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" value="<?= number_format($ticket['price'], 2) ?>" required>
      <div class="invalid-feedback">Please enter a valid price.</div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Update Ticket</button>
  </form>
</div>

<script>
$(document).ready(function(){
  // Bootstrap validation
  (function () {
    'use strict';
    var form = document.getElementById('editTicketForm');
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  })();

  // Dynamically reload ticket types on exhibition change
  $('#exhibition_id').on('change', function () {
    var exhibitionId = $(this).val();
    $('#ticket_type_id').html('<option>Loading...</option>');

    if (exhibitionId) {
      $.ajax({
        url: 'fetch_ticket_types.php',
        method: 'GET',
        data: { exhibition_id: exhibitionId },
        dataType: 'json',
        success: function (data) {
          var options = '<option value="" disabled selected>Select Ticket Type</option>';
          $.each(data, function(i, item) {
            options += '<option value="' + item.id + '">' + item.type + '</option>';
          });
          $('#ticket_type_id').html(options);
        },
        error: function () {
          $('#ticket_type_id').html('<option value="" disabled>Error loading ticket types</option>');
        }
      });
    } else {
      $('#ticket_type_id').html('<option value="" disabled>Select exhibition first</option>');
    }
  });
});
</script>

</body>
</html>

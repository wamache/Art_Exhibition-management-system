<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
include '../config/db.php';

// Sanitize and validate user ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid user ID.");
}

// Fetch user safely
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Optional: Validate email format and role whitelist here

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();
    $stmt->close();

    // Optionally log the update
    // log_action($conn, $_SESSION['user']['id'], 'Updated User', "User ID: $id");

    header("Location: manage_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit User</title>
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
    max-width: 500px;
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
  <h2>Edit User</h2>
  <form method="post" id="editUserForm" novalidate>
    <div class="mb-3">
      <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
      <input
        type="text"
        class="form-control"
        id="name"
        name="name"
        value="<?= htmlspecialchars($user['name']) ?>"
        required
      />
      <div class="invalid-feedback">Please enter the user's name.</div>
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
      <input
        type="email"
        class="form-control"
        id="email"
        name="email"
        value="<?= htmlspecialchars($user['email']) ?>"
        required
      />
      <div class="invalid-feedback">Please enter a valid email address.</div>
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
      <select class="form-select" id="role" name="role" required>
        <option value="">Select Role</option>
        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="artist" <?= $user['role'] === 'artist' ? 'selected' : '' ?>>Artist</option>
        <option value="visitor" <?= $user['role'] === 'visitor' ? 'selected' : '' ?>>Visitor</option>
      </select>
      <div class="invalid-feedback">Please select a role.</div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Update User</button>
  </form>
</div>

<script>
  (function () {
    'use strict';
    const form = document.getElementById('editUserForm');

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

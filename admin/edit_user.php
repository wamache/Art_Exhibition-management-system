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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
  body {
    min-height: 100vh;
    background:
      linear-gradient(
        135deg,
        rgba(38, 70, 83, 0.85),
        rgba(42, 157, 143, 0.85)
      ),
      url('https://www.transparenttextures.com/patterns/diagmonds-light.png') repeat;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 15px;
    color: #f0f0f0;
  }
  .form-container {
    background: #114b5f;
    padding: 30px 35px;
    border-radius: 12px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
  }
  h2 {
    margin-bottom: 30px;
    text-align: center;
    font-weight: 700;
    color: #d4f1f4;
    letter-spacing: 1.05px;
  }
  label {
    font-weight: 600;
    color: #d4f1f4;
  }
  .form-control,
  .form-select {
    background: #3aafa9;
    border: none;
    color: #17252a;
    font-weight: 600;
  }
  .form-control:focus,
  .form-select:focus {
    box-shadow: 0 0 8px #52b69a;
    border: none;
  }
  .invalid-feedback {
    color: #f9f9f9;
    background: #ee6c4d;
    padding: 5px 10px;
    border-radius: 6px;
    margin-top: 4px;
  }
  .btn-primary {
    background-color: #3aafa9;
    border: none;
    font-weight: 700;
    transition: background-color 0.3s ease;
    margin-top: 10px;
  }
  .btn-primary:hover {
    background-color: #2b7a78;
  }
  .btn-back {
    display: inline-block;
    margin-bottom: 25px;
    color: #d4f1f4;
    font-weight: 600;
    text-decoration: none;
    background: transparent;
    border: 2px solid #3aafa9;
    padding: 6px 14px;
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .btn-back:hover {
    background-color: #3aafa9;
    color: #17252a;
    text-decoration: none;
  }
</style>
</head>
<body>

<div class="form-container">
  <a href="manage_users.php" class="btn-back">&#8592; Back to Users</a>
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

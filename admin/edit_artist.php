<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Sanitize and validate ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid artist ID");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $bio = trim($_POST['bio']);
    $contact = trim($_POST['contact']);

    $stmt = $conn->prepare("UPDATE artists SET user_id = ?, bio = ?, contact = ? WHERE id = ?");
    $stmt->bind_param("issi", $user_id, $bio, $contact, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_artists.php");
    exit;
}

// Secure fetch of artist data
$stmt = $conn->prepare("SELECT * FROM artists WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$artist = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$artist) {
    die("Artist not found");
}

// Fetch eligible users
$users = $conn->query("SELECT * FROM users WHERE role = 'artist'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Artist</title>
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
  <h2>Edit Artist</h2>
  <form method="post" id="editArtistForm" novalidate>
    <div class="mb-3">
      <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
      <select id="user_id" name="user_id" class="form-select" required>
        <option value="" disabled>Select user</option>
        <?php while ($user = $users->fetch_assoc()): ?>
            <option value="<?= $user['id'] ?>" <?= $user['id'] == $artist['user_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($user['name']) ?>
            </option>
        <?php endwhile; ?>
      </select>
      <div class="invalid-feedback">Please select a user.</div>
    </div>

    <div class="mb-3">
      <label for="bio" class="form-label">Bio <span class="text-danger">*</span></label>
      <textarea id="bio" name="bio" class="form-control" rows="4" required><?= htmlspecialchars($artist['bio']) ?></textarea>
      <div class="invalid-feedback">Please enter a bio.</div>
    </div>

    <div class="mb-3">
      <label for="contact" class="form-label">Contact <span class="text-danger">*</span></label>
      <input type="text" id="contact" name="contact" class="form-control" value="<?= htmlspecialchars($artist['contact']) ?>" required />
      <div class="invalid-feedback">Please enter contact info.</div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Update Artist</button>
  </form>
</div>

<script>
  (function () {
    'use strict';
    const form = document.getElementById('editArtistForm');

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

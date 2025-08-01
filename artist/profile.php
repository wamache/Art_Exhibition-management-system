<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: ../artist/login.php");
    exit;
}

$user_id = (int) $_SESSION['user']['id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if (empty($name)) {
        $error = "Name is required.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $user_id);
        $stmt->execute();
        $stmt->close();

        $check = $conn->prepare("SELECT user_id FROM artists WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $result = $check->get_result();
        $check->close();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE artists SET bio = ?, contact = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $bio, $contact, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO artists (user_id, bio, contact) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $bio, $contact);
        }

        $stmt->execute();
        $stmt->close();

        $_SESSION['user']['name'] = $name;
        $_SESSION['flash_success'] = "Profile updated successfully.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

$stmt = $conn->prepare("
    SELECT u.name, a.bio, a.contact
    FROM users u
    LEFT JOIN artists a ON u.id = a.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profile</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      margin: 0;
      background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), 
                  url('https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=1500&q=80') no-repeat center center;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
    }

    .container {
      max-width: 600px;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 30px rgba(0,0,0,0.2);
      color: #212529;
      position: relative;
    }

    h2 {
      text-align: center;
      font-weight: 700;
      color: #343a40;
      margin-bottom: 25px;
    }

    .back-btn {
      position: absolute;
      top: 15px;
      right: 20px;
    }

    @media (max-width: 576px) {
      .back-btn {
        position: static;
        margin-bottom: 20px;
        display: block;
        width: 100%;
        text-align: right;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <a href="dashboard.php" class="btn btn-sm btn-secondary back-btn">&larr; Back to Dashboard</a>

    <h2>Edit Profile</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div id="successMsg" class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input
          type="text"
          id="name"
          name="name"
          class="form-control"
          value="<?= htmlspecialchars($user['name'] ?? '') ?>"
          required
        >
      </div>

      <div class="mb-3">
        <label for="bio" class="form-label">Bio</label>
        <textarea
          id="bio"
          name="bio"
          class="form-control"
          rows="4"
        ><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label for="contact" class="form-label">Contact Info</label>
        <input
          type="text"
          id="contact"
          name="contact"
          class="form-control"
          value="<?= htmlspecialchars($user['contact'] ?? '') ?>"
        >
      </div>

      <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
  </div>

  <script>
    $(function(){
      if ($("#successMsg").length) {
        $("#successMsg").fadeIn(400).delay(3000).fadeOut(600);
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
<<<<<<< HEAD
include '../config/db.php';

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $contact = $_POST['contact'];

    $stmt = $conn->prepare("UPDATE users SET name=?, bio=?, contact=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $bio, $contact, $user_id);
    $stmt->execute();

    $_SESSION['user']['name'] = $name;
    $updated = true;
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
=======
require_once '../config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: ../artist/login.php");
    exit;
}

$user_id = (int) $_SESSION['user']['id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if (!empty($name)) {
        // Update users table (name only)
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $user_id);
        $stmt->execute();
        $stmt->close();

        // Check if artist profile exists
        $check = $conn->prepare("SELECT user_id FROM artists WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $result = $check->get_result();
        $check->close();

        if ($result->num_rows > 0) {
            // Update existing artist profile
            $stmt = $conn->prepare("UPDATE artists SET bio = ?, contact = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $bio, $contact, $user_id);
        } else {
            // Insert new artist profile
            $stmt = $conn->prepare("INSERT INTO artists (user_id, bio, contact) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $bio, $contact);
        }
        $stmt->execute();
        $stmt->close();

        // Update session user name
        $_SESSION['user']['name'] = $name;

        // Redirect to dashboard after update
        header("Location: /projects/art_exhibition/artist/dashboard.php");
        exit;
    }
}

// Fetch profile data for the form
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
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
?>
<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            padding: 50px 0;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 30px;
            color: #343a40;
            font-weight: 700;
            text-align: center;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <?php if (!empty($updated)): ?>
            <div id="successMsg" class="alert alert-success text-center" role="alert">
                Profile updated successfully.
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="bio" class="form-label">Bio</label>
                <textarea id="bio" name="bio" class="form-control" rows="4"><?= htmlspecialchars($user['bio']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="contact" class="form-label">Contact Info</label>
                <input type="text" id="contact" name="contact" class="form-control" 
                       value="<?= htmlspecialchars($user['contact']) ?>">
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Profile</button>
        </form>
    </div>

    <script>
        $(document).ready(function(){
            if ($("#successMsg").length) {
                $("#successMsg").fadeIn(500).delay(3000).fadeOut(800);
            }
        });
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
=======
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profile</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      background-color: #f8f9fa;
      padding: 50px 0;
    }
    .container {
      max-width: 600px;
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 30px;
      color: #343a40;
      font-weight: 700;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Profile</h2>

    <form method="post" novalidate>
      <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control"
               value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
      </div>

      <div class="mb-3">
        <label for="bio" class="form-label">Bio</label>
        <textarea id="bio" name="bio" class="form-control" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label for="contact" class="form-label">Contact Info</label>
        <input type="text" id="contact" name="contact" class="form-control"
               value="<?= htmlspecialchars($user['contact'] ?? '') ?>">
      </div>

      <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
</body>
</html>

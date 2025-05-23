<?php
session_start();
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
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
</body>
</html>

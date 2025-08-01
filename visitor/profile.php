<?php
session_start();
include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: visitor_login.php");
    exit;
}

$user = $_SESSION['user'];
$message = '';

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);

    if (empty($newName) || empty($newEmail)) {
        $message = "Name and Email cannot be empty.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Update user info
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $newName, $newEmail, $user['id']);

        if ($stmt->execute()) {
            $_SESSION['user']['name'] = $newName;
            $_SESSION['user']['email'] = $newEmail;
            $user = $_SESSION['user'];
            $message = "✅ Profile updated successfully.";
        } else {
            $message = "❌ Error updating profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Visitor Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #f1f8e9);
            font-family: 'Segoe UI', sans-serif;
        }

        .profile-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert-info {
            background-color: #e8f4fd;
            border-color: #b6e0fe;
            color: #0b486b;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="profile-card w-100" style="max-width: 500px;">
        <h3 class="text-center text-primary mb-4">Your Profile</h3>

        <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control" 
                    required 
                    value="<?= htmlspecialchars($user['name']) ?>"
                />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    required
                    value="<?= htmlspecialchars($user['email']) ?>"
                />
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>

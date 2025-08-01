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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $stmt->close();

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                log_action($conn, $_SESSION['user']['id'], 'Created user', "Email: $email, Role: $role");
                $success = "User successfully added.";
                $name = $email = $role = '';
            } else {
                $error = "Database error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            position: relative;
        }

        /* .btn-back {
            position: absolute;
            top: -45px;
            left: 0;
        } */

        .form-container h2 {
            font-weight: 600;
            color: #2c3e50;
        }

        .form-label {
            font-weight: 500;
        }

        .footer-note {
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 25px;
        }

        .btn-primary {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>
<body>

<div class="form-container">
    <!-- <a href="dashboard.php" class="btn btn-outline-dark btn-sm btn-back">&larr; Back to Dashboard</a> -->
    <div class="mb-3">
    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">&larr; Back to Dashboard</a>
</div>
    <h2 class="text-center mb-4">Add New User</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required
                   value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required
                   value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="At least 6 characters">
        </div>

        <div class="mb-4">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="" disabled <?= !isset($role) ? 'selected' : '' ?>>Select role</option>
                <option value="admin" <?= (isset($role) && $role === 'admin') ? 'selected' : '' ?>>Admin</option>
                <option value="artist" <?= (isset($role) && $role === 'artist') ? 'selected' : '' ?>>Artist</option>
                <option value="visitor" <?= (isset($role) && $role === 'visitor') ? 'selected' : '' ?>>Visitor</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add User</button>
    </form>

    <div class="footer-note">
        &copy; <?= date('Y') ?> AEMS Admin Panel
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

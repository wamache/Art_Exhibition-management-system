<?php
session_start();
include '../config/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $loginError = "Email and password are required!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'artist'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header("Location: dashboard.php");
                exit;
            } else {
                $loginError = "Invalid email or password!";
            }
        } else {
            $loginError = "Invalid email or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Artist Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f2f4f8;
        }
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="mb-4 text-center text-primary">Artist Login</h2>

        <?php if ($loginError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>

        <div class="mt-3 text-center">
            <a href="register.php">Don't have an account? Register</a>
        </div>
    </div>
</body>
</html>

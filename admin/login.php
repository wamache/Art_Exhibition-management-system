<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
<<<<<<< HEAD
=======
                exit;
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
            } else {
                $error = "Access denied: Not an admin.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<<<<<<< HEAD
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <style>
        body {
            background-color: #f1f3f5;
            padding-top: 50px;
        }
        .login-container {
            max-width: 450px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
=======
    <style>
        body {
            background: url('https://i0.wp.com/leaveydesign.com/wp-content/uploads/2024/06/Art-Curator-in-Office-683x1024.png?resize=683%2C1024&ssl=1') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 60px;
        }

        .login-container {
            max-width: 450px;
            margin: auto;
            background: rgba(255, 255, 255, 0.94);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        h2 {
            color: #2c3e50;
        }

        a {
            text-decoration: none;
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 class="text-center mb-4">Admin Login</h2>

    <?php if (isset($error)): ?>
<<<<<<< HEAD
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
=======
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
<<<<<<< HEAD
=======

        <div class="mt-3 text-center">
            <a href="register_admin.php" class="text-primary">Don't have an account? Register</a>
        </div>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    </form>
</div>

</body>
</html>

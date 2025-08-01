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
                exit;
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
    <meta charset="UTF-8" />
    <title>Admin Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('https://i0.wp.com/leaveydesign.com/wp-content/uploads/2024/06/Art-Curator-in-Office-683x1024.png?resize=683%2C1024&ssl=1') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 60px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            max-width: 450px;
            width: 90%;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 35px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        h2 {
            color: #b22222;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }

        .btn-primary {
            background-color: #b22222;
            border-color: #b22222;
        }

        .btn-primary:hover {
            background-color: #8b1a1a;
            border-color: #8b1a1a;
        }

        a.text-primary {
            color: #b22222;
            text-decoration: none;
        }

        a.text-primary:hover {
            text-decoration: underline;
            color: #8b1a1a;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 15px;
            background-color: #b22222;
            color: white;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #8b1a1a;
        }
    </style>
</head>
<body>

<div class="login-container">

    <a href="../index.php" class="back-btn">← Back to Home</a>

    <h2>Admin Login</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" class="form-control" placeholder="admin@example.com" required autofocus>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" placeholder="Your password" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>

        <div class="mt-3 text-center">
            <a href="register_admin.php" class="text-primary">Don't have an account? Register</a>
        </div>
    </form>
</div>

</body>
</html>

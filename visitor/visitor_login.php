<?php
session_start();
<<<<<<< HEAD
include '../config/db.php';
=======
include '../config/db.php'; // Make sure the DB path is correct
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
<<<<<<< HEAD
    $name     = $_POST['name'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? AND role = 'visitor'");
    $stmt->bind_param("s", $name);
=======
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'visitor'");
    $stmt->bind_param("s", $email);
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit;
        } else {
<<<<<<< HEAD
            $error = "Invalid name or password.";
=======
            $error = "Invalid email or password.";
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
        }
    } else {
        $error = "Visitor not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
<<<<<<< HEAD
            background-color: #f4f4f4;
        }
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
=======
            background-image: url('https://images.pexels.com/photos/256381/pexels-photo-256381.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
        }
    </style>
</head>
<body>

<<<<<<< HEAD
<div class="container">
    <div class="login-container">
        <h2 class="text-center text-primary mb-4">Visitor Login</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS and jQuery (optional for future features) -->
=======
<div class="login-container">
    <h2 class="text-center text-primary mb-4">Visitor Login</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
        <div class="mt-3 text-center">
            <a href="visitor_register.php">Don't have an account? Register</a>
        </div>
    </form>
</div>

>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

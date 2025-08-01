<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin';

    // Prepare statement and check for errors
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } else {
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        if ($stmt->execute()) {
            $success = "Admin registered successfully. <a href='login.php' class='alert-link'>Login</a>";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('https://sybaris.com.mx/wp-content/uploads/2022/03/curators-at-the-national-gallery.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 15px;
        }

        .register-container {
            max-width: 500px;
            width: 100%;
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

        a.text-decoration-none {
            color: #b22222;
            font-weight: 600;
        }
        a.text-decoration-none:hover {
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

<div class="register-container">

    <a href="login.php" class="back-btn">← Back to Login</a>

    <h2>Register Admin</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" type="text" name="name" class="form-control" placeholder="Your full name" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control" placeholder="admin@example.com" required>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" placeholder="Choose a secure password" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Register Admin</button>
        </div>

        <div class="mt-3 text-center">
            <a href="login.php" class="text-decoration-none">Already have an account? Log in</a>
        </div>
    </form>
</div>

</body>
</html>

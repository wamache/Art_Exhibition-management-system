<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin';

<<<<<<< HEAD
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();

    echo "<div class='alert alert-success text-center mt-4'>Admin registered successfully. <a href='login.php' class='alert-link'>Login</a></div>";
    exit;
=======
    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);

        echo "<div class='alert alert-success text-center mt-4'>Admin registered successfully. <a href='login.php' class='alert-link'>Login</a></div>";
        exit;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger text-center mt-4'>Error: " . $e->getMessage() . "</div>";
    }
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <style>
        body {
<<<<<<< HEAD
            background-color: #f1f3f5;
            padding-top: 50px;
        }
        .register-container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
=======
            background: url('https://sybaris.com.mx/wp-content/uploads/2022/03/curators-at-the-national-gallery.jpg') no-repeat center center fixed;
            background-size: cover;
            padding-top: 50px;
            font-family: 'Segoe UI', sans-serif;
        }

        .register-container {
            max-width: 500px;
            margin: auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #2c3e50;
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2 class="text-center mb-4">Register Admin</h2>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Register Admin</button>
        </div>
<<<<<<< HEAD
=======
        <div class="mt-3 text-center">
            <a href="login.php" class="text-decoration-none">Already have an account? Log in</a>
        </div>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    </form>
</div>

</body>
</html>

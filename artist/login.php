<?php
session_start();
include '../config/db.php';

// Enable error reporting for debugging (optional, remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name']; // use 'name' instead of 'username'
    $password = $_POST['password'];

    if (empty($name) || empty($password)) {
        echo "Name and password are required!";
        exit;
    }

    // Use 'name' column in the query
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? AND role = 'artist'");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: dashboard.php"); // Redirect to artist dashboard
            exit;
        } else {
            echo "Invalid name or password!";
        }
    } else {
        echo "Invalid name or password!";
    }
}
?>

<h2>Login as Artist</h2>
<form method="POST" action="login.php">
    Name: <input type="text" name="name" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>

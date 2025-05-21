<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../config/db.php';  // Adjust this path if needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        echo "All fields are required!";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Use 'name' instead of 'username' in the query
    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already taken!";
        exit;
    }

    // Insert new artist
    $stmt = $conn->prepare("INSERT INTO users (name, password, email, role) VALUES (?, ?, ?, 'artist')");
    $stmt->bind_param("sss", $name, $hashed_password, $email);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error: Could not register user.";
        exit;
    }
}
?>

<h2>Register as Artist</h2>
<form method="POST" action="register.php">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Register">
</form>

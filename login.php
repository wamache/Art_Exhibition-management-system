<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            // Redirect by role
            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'artist':
                    header('Location: artist/dashboard.php');
                    break;
                case 'visitor':
                    header('Location: visitor/dashboard.php');
                    break;
            }
        } else {
            echo "Invalid credentials.";
        }
    } else {
        echo "User not found.";
    }
}
?>
<form method="post">
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
<<<<<<< HEAD
=======
    <div class="mt-3 text-center">
            <a href="register_admin.php">Don't have an account? Register</a>
        </div>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
</form>

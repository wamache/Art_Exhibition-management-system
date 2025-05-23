<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
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

    // Basic validation
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check for existing email
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
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<h2>Add New User</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post">
    Name: <input type="text" name="name" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>"><br>
    Email: <input type="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"><br>
    Password: <input type="password" name="password" required><br>
    Role:
    <select name="role" required>
        <option value="admin" <?= (isset($role) && $role === 'admin') ? 'selected' : '' ?>>Admin</option>
        <option value="artist" <?= (isset($role) && $role === 'artist') ? 'selected' : '' ?>>Artist</option>
        <option value="visitor" <?= (isset($role) && $role === 'visitor') ? 'selected' : '' ?>>Visitor</option>
    </select><br>
    <input type="submit" value="Add User">
</form>

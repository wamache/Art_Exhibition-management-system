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
<<<<<<< HEAD
=======
                // Clear form inputs after success
                $name = $email = $role = '';
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<<<<<<< HEAD
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
=======
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Add New User</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5" style="max-width: 600px;">
        <h2 class="mb-4">Add New User</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control" 
                    required 
                    value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" 
                />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    required 
                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" 
                />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    required 
                    minlength="6" 
                    placeholder="At least 6 characters"
                />
            </div>

            <div class="mb-4">
                <label for="role" class="form-label">Role</label>
                <select 
                    id="role" 
                    name="role" 
                    class="form-select" 
                    required
                >
                    <option value="" disabled <?= !isset($role) ? 'selected' : '' ?>>Select role</option>
                    <option value="admin" <?= (isset($role) && $role === 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="artist" <?= (isset($role) && $role === 'artist') ? 'selected' : '' ?>>Artist</option>
                    <option value="visitor" <?= (isset($role) && $role === 'visitor') ? 'selected' : '' ?>>Visitor</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add User</button>
        </form>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7

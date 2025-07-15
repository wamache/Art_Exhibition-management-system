<?php
include '../config/db.php';

<<<<<<< HEAD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            echo "Subscribed successfully!";
        } else {
            echo "Error subscribing!";
        }
    } else {
        echo "Invalid email!";
=======
$message = '';
$alertClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $checkStmt = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $message = "You are already subscribed.";
            $alertClass = "alert-warning";
        } else {
            $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $message = "Subscribed successfully!";
                $alertClass = "alert-success";
            } else {
                $message = "Error subscribing. Please try again.";
                $alertClass = "alert-danger";
            }
        }
    } else {
        $message = "Invalid email!";
        $alertClass = "alert-danger";
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    }
}
?>

<<<<<<< HEAD
<h2>Subscribe for Updates</h2>
<form method="POST">
    Email: <input type="email" name="email" required>
    <input type="submit" value="Subscribe">
</form>
=======
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Subscribe for Updates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="w-100" style="max-width: 400px;">
        <h2 class="mb-4 text-center">Subscribe for Updates</h2>

        <?php if ($message): ?>
            <div class="alert <?= htmlspecialchars($alertClass) ?> text-center" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    required 
                    autofocus
                >
            </div>
            <button type="submit" class="btn btn-primary w-100">Subscribe</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7

<?php
include '../config/db.php';

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
    }
}
?>

<h2>Subscribe for Updates</h2>
<form method="POST">
    Email: <input type="email" name="email" required>
    <input type="submit" value="Subscribe">
</form>

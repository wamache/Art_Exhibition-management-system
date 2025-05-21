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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $price = $_POST['price'];
    $exhibition_id = $_POST['exhibition_id'];

    $stmt = $conn->prepare("INSERT INTO ticket_types (type, price, exhibition_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $type, $price, $exhibition_id);
    $stmt->execute();
    header("Location: manage_ticket_types.php");
    exit;
}

$exhibitions = $conn->query("SELECT * FROM exhibitions");
?>
<h2>Add New Ticket Type for Exhibition</h2>
<form method="post">
    Type:
    <select name="type" required>
        <option value="Standard">Standard</option>
        <option value="VIP">VIP</option>
    </select><br>
    Price: <input type="number" step="0.01" name="price" required><br>
    Exhibition:
    <select name="exhibition_id" required>
        <?php while ($ex = $exhibitions->fetch_assoc()): ?>
            <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['title']) ?></option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Create Ticket Type">
</form>

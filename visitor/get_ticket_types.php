<?php
include '../config/db.php';

if (!isset($_GET['exhibition_id'])) {
    echo "<option value=''>No exhibition selected</option>";
    exit;
}

$exhibition_id = intval($_GET['exhibition_id']);

$stmt = $conn->prepare("SELECT id, type, price FROM ticket_types WHERE exhibition_id = ?");
$stmt->bind_param("i", $exhibition_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($ticket = $result->fetch_assoc()) {
        $type = htmlspecialchars($ticket['type']);
        $price = htmlspecialchars($ticket['price']);
        echo "<option value=\"{$type}\">{$type} - \${$price}</option>";
    }
} else {
    echo "<option value=''>No tickets available</option>";
}

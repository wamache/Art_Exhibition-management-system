<?php
include '../config/db.php';

$exhibition_id = isset($_GET['exhibition_id']) ? intval($_GET['exhibition_id']) : 0;
if ($exhibition_id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, type FROM ticket_types WHERE exhibition_id = ? ORDER BY type");
$stmt->bind_param("i", $exhibition_id);
$stmt->execute();
$result = $stmt->get_result();

$types = [];
while ($row = $result->fetch_assoc()) {
    $types[] = $row;
}

$stmt->close();
header('Content-Type: application/json');
echo json_encode($types);

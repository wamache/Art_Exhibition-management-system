<?php
session_start();

// Access control: Only admin allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}

include '../config/db.php';

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['exhibition_id'], $_POST['artwork_id'])) {
    die("Invalid request");
}

$exhibition_id = intval($_POST['exhibition_id']);
$artwork_id = intval($_POST['artwork_id']);

// Check if the assignment already exists
$check = $conn->prepare("SELECT 1 FROM exhibition_artworks WHERE exhibition_id = ? AND artwork_id = ?");
if (!$check) {
    die("Prepare failed: " . $conn->error);
}

$check->bind_param("ii", $exhibition_id, $artwork_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&error=exists");
    exit;
}
$check->close();

// Logging helper function
function log_action($conn, $user_id, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iss", $user_id, $action, $details);
        $stmt->execute();
        $stmt->close();
    }
}

// Insert the new assignment
$stmt = $conn->prepare("INSERT INTO exhibition_artworks (exhibition_id, artwork_id) VALUES (?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $exhibition_id, $artwork_id);

if ($stmt->execute()) {
    // Log the assignment action
    log_action($conn, $_SESSION['user']['id'], 'Assigned artwork to exhibition', "Exhibition ID: $exhibition_id, Artwork ID: $artwork_id");
    $stmt->close();

    // Redirect back with success message
    header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id&success=1");
    exit;
} else {
    $error = "Error inserting assignment: " . $stmt->error;
    $stmt->close();
    die($error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Exhibition Artworks | Admin</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #f8f9fa, #e2e6ea);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    color: #333;
  }
  .container {
    background: white;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    max-width: 500px;
    text-align: center;
  }
  .back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 18px;
    background-color: #b22222;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
  }
  .back-btn:hover {
    background-color: #8b1a1a;
  }
  p {
    font-size: 1.1rem;
  }
</style>
</head>
<body>
  <div class="container">
    <a href="manage_exhibition_artworks.php?exhibition_id=<?= htmlspecialchars($exhibition_id) ?>" class="back-btn">← Back</a>
    <p><!-- place error or success message here --></p>
  </div>
</body>
</html>

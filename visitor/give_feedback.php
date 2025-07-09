<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$message = '';

// Get exhibitions the user has a ticket for
$exhibitions = $conn->prepare("
    SELECT e.id, e.title 
    FROM exhibitions e
    JOIN tickets t ON t.exhibition_id = e.id
    WHERE t.visitor_id = ?
    GROUP BY e.id
");
$exhibitions->bind_param("i", $user_id);
$exhibitions->execute();
$exhibitions_result = $exhibitions->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exhibition_id = $_POST['exhibition_id'];
    $rating = (int) $_POST['rating'];
    $comment = trim($_POST['comment']);

    // Check for duplicate feedback
    $check = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND exhibition_id = ?");
    $check->bind_param("ii", $user_id, $exhibition_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "<div class='alert alert-warning'>You’ve already reviewed this exhibition.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, exhibition_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $exhibition_id, $rating, $comment);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Thanks for your feedback!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error submitting review. Please try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Leave Feedback</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5" style="max-width: 600px;">
    <h2 class="mb-4">Leave Feedback</h2>

    <?= $message ?>

    <form method="POST">
        <div class="mb-3">
            <label for="exhibition_id" class="form-label">Exhibition</label>
            <select name="exhibition_id" id="exhibition_id" class="form-select" required>
                <option value="">-- Select Exhibition --</option>
                <?php while ($row = $exhibitions_result->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                <option value="">-- Select Rating --</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Comment (optional)</label>
            <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Your thoughts..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit Feedback</button>
    </form>
</div>
</body>
</html>

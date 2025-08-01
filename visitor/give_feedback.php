<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$message = '';

// Fetch all exhibitions for dropdown
$exhibitions_result = $conn->query("SELECT id, title FROM exhibitions ORDER BY start_date DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exhibition_id = (int) $_POST['exhibition_id'];
    $rating = (int) $_POST['rating'];
    $comment = trim($_POST['comment']);

    // Optional: Verify ticket ownership before allowing review
    $ticket_check = $conn->prepare("SELECT id FROM tickets WHERE visitor_id = ? AND exhibition_id = ?");
    $ticket_check->bind_param("ii", $user_id, $exhibition_id);
    $ticket_check->execute();
    $ticket_check->store_result();

    if ($ticket_check->num_rows === 0) {
        $message = "<div class='alert alert-danger'>You cannot review an exhibition you don't have a ticket for.</div>";
    } else {
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Leave Feedback</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <style>
        /* Background image with dark overlay */
        body {
            background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .overlay {
            background-color: rgba(0,0,0,0.6);
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
        }
        .feedback-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 30px 35px;
        }
        h2 {
            color: #007bff;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        label {
            font-weight: 600;
            color: #444;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 15px;
            font-weight: 600;
        }
        .back-btn {
            margin-bottom: 20px;
            text-align: left;
        }
        .back-btn a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .back-btn a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="feedback-container shadow-sm">
        <div class="back-btn">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
        </div>

        <h2>Leave Feedback</h2>

        <?= $message ?>

        <form method="POST" novalidate>
            <div class="mb-4">
                <label for="exhibition_id" class="form-label">Exhibition</label>
                <select name="exhibition_id" id="exhibition_id" class="form-select" required>
                    <option value="">-- Select Exhibition --</option>
                    <?php while ($row = $exhibitions_result->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select" required>
                    <option value="">-- Select Rating --</option>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="comment" class="form-label">Comment (optional)</label>
                <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Your thoughts..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit Feedback</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

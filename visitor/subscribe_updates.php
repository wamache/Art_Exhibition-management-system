<?php
include '../config/db.php';

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
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        $message = "Invalid email!";
        $alertClass = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Subscribe for Updates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .subscribe-container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            color: #5a2a83;
            margin-bottom: 25px;
            font-weight: 700;
        }
        .btn-primary {
            background-color: #5a2a83;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #421e5a;
        }
        .alert {
            font-weight: 600;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        label {
            font-weight: 600;
            color: #444;
        }
        input::placeholder {
            font-style: italic;
            color: #aaa;
        }
        .btn-outline-secondary {
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background-color: #5a2a83;
            color: white;
            border-color: #5a2a83;
        }
    </style>
</head>
<body>
    <div class="subscribe-container shadow-sm">
        <h2>Subscribe for Updates</h2>

        <?php if ($message): ?>
            <div class="alert <?= htmlspecialchars($alertClass) ?>" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4 text-start">
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

        <a href="dashboard.php" class="btn btn-outline-secondary mt-3 w-100">← Back to Dashboard</a>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

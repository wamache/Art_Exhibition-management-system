<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

$exhibitions = $conn->query("SELECT * FROM exhibitions");
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visitor_id    = $_SESSION['user']['id'];
    $exhibition_id = $_POST['exhibition_id'];
    $ticket_type   = $_POST['ticket_type'];

    $checkStmt = $conn->prepare("SELECT id FROM tickets WHERE visitor_id = ? AND exhibition_id = ? AND ticket_type = ?");
    $checkStmt->bind_param("iis", $visitor_id, $exhibition_id, $ticket_type);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $message = "<div class='alert alert-warning'>⚠️ You have already purchased this ticket type for the selected exhibition.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO tickets (visitor_id, exhibition_id, ticket_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $visitor_id, $exhibition_id, $ticket_type);

        if ($stmt->execute()) {
            $ticket_id = $stmt->insert_id;

            $saleStmt = $conn->prepare("INSERT INTO sales (ticket_id, user_id, sale_date) VALUES (?, ?, NOW())");
            $saleStmt->bind_param("ii", $ticket_id, $visitor_id);
            if ($saleStmt->execute()) {
                header("Location: generate_ticket_pdf.php?ticket_id=$ticket_id");
                exit;
            } else {
                $message = "<div class='alert alert-danger'>❌ Failed to record the sale.</div>";
            }
            $saleStmt->close();
        } else {
            $message = "<div class='alert alert-danger'>❌ Failed to purchase ticket.</div>";
        }
        $stmt->close();
    }
    $checkStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Ticket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1523906834658-6e24ef2386f9?auto=format&fit=crop&w=1950&q=80') no-repeat center center fixed;
            background-size: cover;
        }

        .form-container {
            background-color: rgba(255,255,255,0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }

        .back-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-6 form-container">
        <h2 class="text-center mb-4 text-primary">🎟 Purchase Ticket</h2>

        <?= $message ?>

        <form method="POST">
            <div class="mb-3">
                <label for="exhibition_id" class="form-label">Exhibition</label>
                <select name="exhibition_id" id="exhibition_id" class="form-select" required>
                    <option value="">-- Select Exhibition --</option>
                    <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($ex['id']) ?>"><?= htmlspecialchars($ex['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="ticket_type" class="form-label">Ticket Type</label>
                <select name="ticket_type" id="ticket_type" class="form-select" required>
                    <option value="">-- Select Ticket Type --</option>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success">Buy Ticket</button>
            </div>
        </form>

        <div class="text-center back-btn">
            <a href="dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function fetchTicketTypes(exhibitionId) {
        if (!exhibitionId) return;

        $.get("get_ticket_types.php?exhibition_id=" + exhibitionId, function (data) {
            $("#ticket_type").html(data);
        });
    }

    $('#exhibition_id').on('change', function () {
        fetchTicketTypes(this.value);
    });
</script>

</body>
</html>

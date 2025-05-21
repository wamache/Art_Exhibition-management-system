<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

$exhibitions = $conn->query("SELECT * FROM exhibitions");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visitor_id    = $_SESSION['user']['id'];
    $exhibition_id = $_POST['exhibition_id'];
    $ticket_type   = $_POST['ticket_type'];

    $stmt = $conn->prepare("INSERT INTO tickets (visitor_id, exhibition_id, ticket_type) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $visitor_id, $exhibition_id, $ticket_type);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>🎟 Ticket purchased successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Failed to purchase ticket.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Ticket</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script>
        function fetchTicketTypes(exhibitionId) {
            if (!exhibitionId) return;

            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_ticket_types.php?exhibition_id=" + exhibitionId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const ticketDropdown = document.getElementById("ticket_type");
                    ticketDropdown.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Purchase Ticket</h2>

    <?php if (isset($message)) echo $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="exhibition_id" class="form-label">Exhibition</label>
            <select name="exhibition_id" id="exhibition_id" class="form-select" required onchange="fetchTicketTypes(this.value)">
                <option value="">-- Select Exhibition --</option>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="ticket_type" class="form-label">Ticket Type</label>
            <select name="ticket_type" id="ticket_type" class="form-select" required>
                <option value="">-- Select Ticket Type --</option>
                <!-- Options will be loaded dynamically -->
            </select>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Buy Ticket</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

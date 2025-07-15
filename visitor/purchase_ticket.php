<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'visitor') {
    header("Location: ../visitor_login.php");
    exit;
}

$exhibitions = $conn->query("SELECT * FROM exhibitions");

<<<<<<< HEAD
=======
$showSuccessAndRedirect = false;
$message = '';

>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visitor_id    = $_SESSION['user']['id'];
    $exhibition_id = $_POST['exhibition_id'];
    $ticket_type   = $_POST['ticket_type'];

<<<<<<< HEAD
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
=======
    // Check if visitor already purchased this ticket type for this exhibition
    $checkStmt = $conn->prepare("SELECT id FROM tickets WHERE visitor_id = ? AND exhibition_id = ? AND ticket_type = ?");
    $checkStmt->bind_param("iis", $visitor_id, $exhibition_id, $ticket_type);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Duplicate ticket type purchase attempt
        $message = "<div class='alert alert-warning'>⚠️ You have already purchased this ticket type for the selected exhibition.</div>";
    } else {
        // Insert new ticket
        $stmt = $conn->prepare("INSERT INTO tickets (visitor_id, exhibition_id, ticket_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $visitor_id, $exhibition_id, $ticket_type);

        if ($stmt->execute()) {
            $ticket_id = $stmt->insert_id;

            // Insert into sales
            $saleStmt = $conn->prepare("INSERT INTO sales (ticket_id, user_id, sale_date) VALUES (?, ?, NOW())");
            $saleStmt->bind_param("ii", $ticket_id, $visitor_id);

            if ($saleStmt->execute()) {
                $showSuccessAndRedirect = true;
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Purchase Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7

    <script>
        function fetchTicketTypes(exhibitionId) {
            if (!exhibitionId) return;

            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_ticket_types.php?exhibition_id=" + exhibitionId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
<<<<<<< HEAD
                    const ticketDropdown = document.getElementById("ticket_type");
                    ticketDropdown.innerHTML = xhr.responseText;
=======
                    document.getElementById("ticket_type").innerHTML = xhr.responseText;
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
                }
            };
            xhr.send();
        }
<<<<<<< HEAD
    </script>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Purchase Ticket</h2>

    <?php if (isset($message)) echo $message; ?>

=======

        <?php if ($showSuccessAndRedirect): ?>
        setTimeout(function() {
            window.location.href = 'dashboard.php';
        }, 3000);
        <?php endif; ?>
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Purchase Ticket</h2>

    <?php 
    if ($showSuccessAndRedirect) {
        echo "<div class='alert alert-success'>🎟 Ticket purchased successfully! Redirecting to dashboard...</div>";
    } elseif (!empty($message)) {
        echo $message;
    }
    ?>

    <?php if (!$showSuccessAndRedirect): ?>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    <form method="POST">
        <div class="mb-3">
            <label for="exhibition_id" class="form-label">Exhibition</label>
            <select name="exhibition_id" id="exhibition_id" class="form-select" required onchange="fetchTicketTypes(this.value)">
                <option value="">-- Select Exhibition --</option>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
<<<<<<< HEAD
                    <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['title']) ?></option>
=======
                    <option value="<?= htmlspecialchars($ex['id']) ?>"><?= htmlspecialchars($ex['title']) ?></option>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="ticket_type" class="form-label">Ticket Type</label>
            <select name="ticket_type" id="ticket_type" class="form-select" required>
                <option value="">-- Select Ticket Type --</option>
<<<<<<< HEAD
                <!-- Options will be loaded dynamically -->
=======
                <!-- Populated via AJAX -->
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
            </select>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Buy Ticket</button>
        </div>
    </form>
<<<<<<< HEAD
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

=======
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
</body>
</html>

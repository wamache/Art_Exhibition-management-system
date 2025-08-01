<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$user_id = $_SESSION['user']['id'];

// Step 1: Get artist_id from the artists table for this user
$stmt = $conn->prepare("SELECT id FROM artists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$artist = $res->fetch_assoc();
$stmt->close();

if (!$artist) {
    die("Artist profile not found.");
}

$artist_id = (int)$artist['id'];

// Step 2: Fetch artworks for this artist_id
$stmt = $conn->prepare("SELECT id, title FROM artworks WHERE artist_id = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$artworks = $stmt->get_result();
$stmt->close();

// Step 3: Fetch exhibitions (assuming all exhibitions)
$exhibitions = $conn->query("SELECT id, title FROM exhibitions");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artwork_id = intval($_POST['artwork_id']);
    $exhibition_id = intval($_POST['exhibition_id']);

    if ($artwork_id && $exhibition_id) {
        $stmt = $conn->prepare("INSERT IGNORE INTO exhibition_artworks (exhibition_id, artwork_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $exhibition_id, $artwork_id);
        if ($stmt->execute()) {
            $message = "Artwork submitted successfully!";
        } else {
            $message = "Error submitting artwork.";
        }
        $stmt->close();
    } else {
        $message = "Please select both artwork and exhibition.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Submit Artwork to Exhibition</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    body {
        background:
            url('https://www.transparenttextures.com/patterns/cubes.png'),
            linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .form-container {
        max-width: 480px;
        width: 100%;
        background: white;
        padding: 2.5rem 3rem;
        border-radius: 1rem;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    h2 {
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 2rem;
    }
    .btn-primary {
        width: 100%;
        font-weight: 600;
        padding: 0.75rem;
        font-size: 1.1rem;
        border-radius: 0.5rem;
    }
    .alert {
        text-align: left;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }
    .back-btn {
        display: inline-block;
        margin-bottom: 1.5rem;
        text-decoration: none;
        color: #1e3a8a;
        font-weight: 600;
    }
    .back-btn:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="form-container">
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

    <h2>Submit Artwork to Exhibition</h2>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" id="submitArtworkForm" novalidate>
        <div class="mb-4 text-start">
            <label for="artwork_id" class="form-label fw-semibold">Select Artwork</label>
            <select name="artwork_id" id="artwork_id" class="form-select" required>
                <option value="" disabled selected>-- Choose Artwork --</option>
                <?php while ($art = $artworks->fetch_assoc()): ?>
                    <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-5 text-start">
            <label for="exhibition_id" class="form-label fw-semibold">Select Exhibition</label>
            <select name="exhibition_id" id="exhibition_id" class="form-select" required>
                <option value="" disabled selected>-- Choose Exhibition --</option>
                <?php while ($ex = $exhibitions->fetch_assoc()): ?>
                    <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit Artwork</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('submitArtworkForm').addEventListener('submit', function(e) {
        const artwork = document.getElementById('artwork_id').value;
        const exhibition = document.getElementById('exhibition_id').value;

        if (!artwork || !exhibition) {
            e.preventDefault();
            alert('Please select both artwork and exhibition.');
        }
    });
</script>

</body>
</html>

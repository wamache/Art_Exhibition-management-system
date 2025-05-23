<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];

$artworks = $conn->query("SELECT * FROM artworks WHERE artist_id = $artist_id");
$exhibitions = $conn->query("SELECT * FROM exhibitions");

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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; padding: 2rem; }
    .form-container {
        max-width: 500px;
        margin: auto;
        background: white;
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0,0,0,.1);
    }
</style>
</head>
<body>

<div class="form-container">
    <h2 class="mb-4">Submit Artwork to Exhibition</h2>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" id="submitArtworkForm">
        <div class="mb-3">
            <label for="artwork_id" class="form-label">Artwork</label>
            <select name="artwork_id" id="artwork_id" class="form-select" required>
                <option value="">-- Select Artwork --</option>
                <?php
                // Reset result pointer before looping (in case of prior iteration)
                $artworks->data_seek(0);
                while ($art = $artworks->fetch_assoc()): ?>
                    <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="exhibition_id" class="form-label">Exhibition</label>
            <select name="exhibition_id" id="exhibition_id" class="form-select" required>
                <option value="">-- Select Exhibition --</option>
                <?php
                $exhibitions->data_seek(0);
                while ($ex = $exhibitions->fetch_assoc()): ?>
                    <option value="<?= $ex['id'] ?>"><?= htmlspecialchars($ex['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
    $('#submitArtworkForm').on('submit', function(e){
        if (!$('#artwork_id').val() || !$('#exhibition_id').val()) {
            e.preventDefault();
            alert('Please select both artwork and exhibition.');
        }
    });
});
</script>

</body>
</html>

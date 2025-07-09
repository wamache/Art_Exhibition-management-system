<?php
session_start();
include '../config/db.php';
$artist_id = $_SESSION['user']['id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch artwork safely with prepared stmt
$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ? AND artist_id = ?");
$stmt->bind_param("ii", $id, $artist_id);
$stmt->execute();
$art = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$art) die("Not found or access denied.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $medium = $_POST['medium'];
    $year_created = $_POST['year_created'];
    $description = $_POST['description'];

    $image = $art['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = "../uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $stmt = $conn->prepare("UPDATE artworks SET title=?, medium=?, year_created=?, description=?, image=? WHERE id=? AND artist_id=?");
    $stmt->bind_param("ssissii", $title, $medium, $year_created, $description, $image, $id, $artist_id);
    $stmt->execute();

    header("Location: manage_artworks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Artwork</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        .current-image {
            max-width: 150px;
            max-height: 150px;
            object-fit: contain;
            margin-bottom: 1rem;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2 class="mb-4">Edit Artwork</h2>
    <form method="post" enctype="multipart/form-data" id="editArtworkForm">
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($art['title']) ?>">
        </div>
        <div class="mb-3">
            <label for="medium" class="form-label">Medium</label>
            <input type="text" id="medium" name="medium" class="form-control" value="<?= htmlspecialchars($art['medium']) ?>">
        </div>
        <div class="mb-3">
            <label for="year_created" class="form-label">Year Created</label>
            <input type="number" id="year_created" name="year_created" class="form-control" value="<?= $art['year_created'] ?>">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control"><?= htmlspecialchars($art['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Image:</label><br>
            <?php if ($art['image']): ?>
                <img src="../uploads/<?= htmlspecialchars($art['image']) ?>" alt="Artwork Image" class="current-image" id="currentImagePreview">
            <?php else: ?>
                <p>No image uploaded.</p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Replace Image</label>
            <input type="file" id="image" name="image" class="form-control">
            <small class="form-text text-muted">Upload a new image to replace the current one.</small>
        </div>

        <button type="submit" class="btn btn-primary">Update Artwork</button>
        <a href="manage_artworks.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Preview new image when selected
    $('#image').on('change', function(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if ($('#currentImagePreview').length) {
                    $('#currentImagePreview').attr('src', e.target.result);
                } else {
                    $('<img>', {
                        src: e.target.result,
                        class: 'current-image',
                        id: 'currentImagePreview'
                    }).insertBefore('#image');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

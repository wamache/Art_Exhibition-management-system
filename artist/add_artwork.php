<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$artist_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $medium = trim($_POST['medium']);
    $year_created = intval($_POST['year_created']);
    $description = trim($_POST['description']);
    $image = '';

    if (empty($title)) {
        die("Title is required.");
    }

    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);
        $file_size = $_FILES['image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            die("Only JPG, PNG, and GIF images are allowed.");
        }

        if ($file_size > 5 * 1024 * 1024) {
            die("File size must be less than 5MB.");
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid('artwork_', true) . '.' . $ext;
        $target = "../uploads/" . $image;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            die("Failed to upload image.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO artworks (artist_id, title, medium, year_created, description, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississ", $artist_id, $title, $medium, $year_created, $description, $image);

    if ($stmt->execute()) {
        header("Location: manage_artworks.php?success=1");
        exit;
    } else {
        die("Database error: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add New Artwork</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body {
    background-color: #f8f9fa;
  }
  .form-container {
    max-width: 600px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgb(0 0 0 / 0.1);
  }
  h2 {
    margin-bottom: 25px;
    color: #343a40;
    font-weight: 700;
  }
</style>
</head>
<body>

<div class="form-container">
  <h2>Add New Artwork</h2>
  <form method="post" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
      <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
      <input 
        type="text" 
        class="form-control" 
        id="title" 
        name="title" 
        required 
        value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"
        placeholder="Enter artwork title"
      >
    </div>

    <div class="mb-3">
      <label for="medium" class="form-label">Medium</label>
      <input 
        type="text" 
        class="form-control" 
        id="medium" 
        name="medium" 
        value="<?= isset($_POST['medium']) ? htmlspecialchars($_POST['medium']) : '' ?>"
        placeholder="E.g., Oil on Canvas"
      >
    </div>

    <div class="mb-3">
      <label for="year_created" class="form-label">Year Created</label>
      <input 
        type="number" 
        class="form-control" 
        id="year_created" 
        name="year_created" 
        min="0" 
        max="<?= date('Y') ?>" 
        value="<?= isset($_POST['year_created']) ? (int)$_POST['year_created'] : '' ?>"
        placeholder="E.g., 2023"
      >
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea 
        class="form-control" 
        id="description" 
        name="description" 
        rows="4"
        placeholder="Describe your artwork"
      ><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
    </div>

    <div class="mb-4">
      <label for="image" class="form-label">Upload Image</label>
      <input 
        class="form-control" 
        type="file" 
        id="image" 
        name="image" 
        accept="image/*"
      >
      <div class="form-text">Allowed types: JPG, PNG, GIF. Max size: 5MB.</div>
    </div>

    <button type="submit" class="btn btn-primary">Add Artwork</button>
  </form>
</div>

<!-- Bootstrap JS Bundle (Popper + Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

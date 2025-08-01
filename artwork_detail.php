<?php
require_once 'config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("
  SELECT a.*, u.name AS artist_name
  FROM artworks a
  LEFT JOIN artists ar ON a.artist_id = ar.id
  LEFT JOIN users u ON ar.user_id = u.id
  WHERE a.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$art = $result->fetch_assoc();

$conn->close();

if (!$art) {
    die("Artwork not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($art['title']) ?> | Art Gallery</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: #f7f9fc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .artwork-img {
      max-height: 450px;
      object-fit: contain;
      border-radius: 8px;
      background: white;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
      width: 100%;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">Art Gallery</a>
  </div>
</nav>

<div class="container my-5">
  <a href="index.php" class="btn btn-secondary mb-4">&larr; Back to Gallery</a>

  <div class="row">
    <div class="col-md-6 mb-4">
      <img src="uploads/artworks/<?= htmlspecialchars($art['image'] ?: 'default.jpg') ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="artwork-img" />
    </div>
    <div class="col-md-6">
      <h1><?= htmlspecialchars($art['title']) ?></h1>
      <p class="text-muted">By <?= htmlspecialchars($art['artist_name'] ?? 'Unknown Artist') ?></p>
      <p class="h4 text-success">
        <?= isset($art['price']) && $art['price'] > 0 ? 'KSh ' . number_format($art['price']) : 'Price on Request' ?>
      </p>
      <p><?= nl2br(htmlspecialchars($art['description'])) ?></p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

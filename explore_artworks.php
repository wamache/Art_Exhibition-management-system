<?php
require_once 'config/db.php';

$sql = "
  SELECT a.*, u.name AS artist_name
  FROM artwork a
  LEFT JOIN artists ar ON a.artist_id = ar.id
  LEFT JOIN users u ON ar.user_id = u.id
  ORDER BY a.created_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Explore Artworks | AEMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .art-card img {
      height: 250px;
      object-fit: cover;
    }
    .art-card .card-body {
      min-height: 200px;
    }
  </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">AEMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="explore_artworks.php">Explore Artworks</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Artworks Grid -->
<div class="container my-5">
  <h2 class="text-center mb-5">Explore Artworks</h2>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($art = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card h-100 art-card shadow-sm">
            <img src="uploads/artworks/<?= htmlspecialchars($art['image'] ?: 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($art['title']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($art['title']) ?></h5>
              <p class="card-text text-muted">
                <?= htmlspecialchars($art['artist_name'] ?? 'Unknown Artist') ?>
              </p>
              <p class="card-text">
                <?= nl2br(htmlspecialchars(mb_strimwidth($art['description'], 0, 100, '...'))) ?>
              </p>
              <a href="artwork_detail.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col">
        <p>No artworks available right now.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Bootstrap JS (optional for navbar) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

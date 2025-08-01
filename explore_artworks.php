<?php
require_once 'config/db.php';

$sql = "
  SELECT a.*, u.name AS artist_name
  FROM artworks a
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
    body {
      background: linear-gradient(to right, #f0f4f8, #e0eafc);
      font-family: 'Segoe UI', sans-serif;
    }

    .art-card img {
      height: 250px;
      object-fit: cover;
    }

    .art-card .card-body {
      min-height: 220px;
    }

    .art-card .card-title {
      font-weight: 600;
    }

    .art-card {
      border: none;
      transition: 0.3s ease;
    }

    .art-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .price-tag {
      font-weight: bold;
      color: #198754; /* Bootstrap success green */
    }

    .artwork-wrapper {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
  </style>
</head>
<body>

<!-- Navbar -->
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

<!-- Artworks -->
<div class="container my-5">
  <div class="artwork-wrapper">
    <h2 class="text-center mb-5 text-primary">Explore Artworks</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($art = $result->fetch_assoc()): ?>
          <div class="col">
            <div class="card h-100 art-card">
              <img src="uploads/artworks/<?= htmlspecialchars($art['image'] ?: 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($art['title']) ?>">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($art['title']) ?></h5>
                <p class="text-muted mb-1">by <?= htmlspecialchars($art['artist_name'] ?? 'Unknown Artist') ?></p>
                <p class="price-tag mb-2">
                  <?= isset($art['price']) && $art['price'] > 0 ? 'KSh ' . number_format($art['price']) : 'Not for Sale' ?>
                </p>
                <p class="card-text small mb-3"><?= nl2br(htmlspecialchars(mb_strimwidth($art['description'], 0, 100, '...'))) ?></p>
                <a href="artwork_detail.php?id=<?= $art['id'] ?>" class="btn btn-outline-primary btn-sm mt-auto">View Details</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center">
            No artworks available right now.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

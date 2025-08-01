<?php
require_once 'config/db.php';

// Fetch all exhibitions
$result = $conn->query("SELECT * FROM exhibitions ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Exhibitions | ArtExhibitPro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e0ecf8, #f8f9fa);
      font-family: 'Segoe UI', sans-serif;
    }

    .card img {
      height: 200px;
      object-fit: cover;
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .card {
      transition: 0.3s ease-in-out;
      border: none;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .back-btn {
      margin-bottom: 30px;
    }

    .exhibitions-wrapper {
      padding: 30px;
      background-color: #ffffffcc;
      border-radius: 10px;
    }
  </style>
</head>
<body>

<div class="container my-5 exhibitions-wrapper">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">Current & Upcoming Exhibitions</h2>
    <a href="index.php" class="btn btn-outline-secondary back-btn">← Back</a>
  </div>

  <div class="row">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($exhibit = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img src="uploads/<?= htmlspecialchars($exhibit['image'] ?: 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($exhibit['title']) ?>">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($exhibit['title']) ?></h5>
              <p class="text-muted mb-2">
                <?= date("M j, Y", strtotime($exhibit['start_date'])) ?> – 
                <?= date("M j, Y", strtotime($exhibit['end_date'])) ?>
              </p>
              <p class="small text-muted mb-3"><?= htmlspecialchars($exhibit['location']) ?></p>
              <a href="exhibition_details.php?id=<?= $exhibit['id'] ?>" class="btn btn-outline-primary mt-auto">View Details</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center">
          No exhibitions found.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

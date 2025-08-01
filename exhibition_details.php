<?php
require_once 'config/db.php';

// Get exhibition ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare and execute the query
$stmt = $conn->prepare("SELECT * FROM exhibitions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exhibit = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $exhibit ? htmlspecialchars($exhibit['title']) : 'Exhibition Not Found' ?> | ArtExhibitPro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f5f7fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .exhibit-img {
      max-height: 400px;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .details-box {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .back-btn {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="container my-5">
  <?php if ($exhibit): ?>
    <div class="row align-items-center">
      <div class="col-md-6 mb-4">
        <img src="uploads/<?= htmlspecialchars($exhibit['image'] ?: 'default.jpg') ?>" alt="<?= htmlspecialchars($exhibit['title']) ?>" class="img-fluid exhibit-img">
      </div>
      <div class="col-md-6">
        <div class="details-box">
          <h2 class="text-primary mb-3"><?= htmlspecialchars($exhibit['title']) ?></h2>
          <p class="text-muted">
            <strong>Dates:</strong>
            <?= date("F j, Y", strtotime($exhibit['start_date'])) ?> – <?= date("F j, Y", strtotime($exhibit['end_date'])) ?><br>
            <strong>Location:</strong> <?= htmlspecialchars($exhibit['location']) ?>
          </p>
          <hr>
          <p><?= nl2br(htmlspecialchars($exhibit['description'])) ?></p>
          <a href="exhibition.php" class="btn btn-secondary back-btn">← Back to All Exhibitions</a>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">
      <h4>Exhibition Not Found</h4>
      <p>The exhibition you're looking for does not exist or has been removed.</p>
      <a href="exhibition.php" class="btn btn-primary">Back to Exhibitions</a>
    </div>
  <?php endif; ?>
</div>

</body>
</html>

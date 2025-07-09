<?php
require_once 'config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
</head>
<body>

<div class="container my-5">
  <?php if ($exhibit): ?>
    <div class="row">
      <div class="col-md-6">
        <img src="uploads/<?= htmlspecialchars($exhibit['image'] ?: 'default.jpg') ?>" alt="<?= htmlspecialchars($exhibit['title']) ?>" class="img-fluid rounded">
      </div>
      <div class="col-md-6">
        <h1><?= htmlspecialchars($exhibit['title']) ?></h1>
        <p class="text-muted">
          <?= date("F j, Y", strtotime($exhibit['start_date'])) ?>
          –
          <?= date("F j, Y", strtotime($exhibit['end_date'])) ?>
          |
          <?= htmlspecialchars($exhibit['location']) ?>
        </p>
        <p><?= nl2br(htmlspecialchars($exhibit['description'])) ?></p>
        <a href="index.php" class="btn btn-secondary mt-3">← Back to Exhibitions</a>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">
      <h4>Exhibition Not Found</h4>
      <p>The exhibition you're looking for does not exist or has been removed.</p>
      <a href="index.php" class="btn btn-primary">Back to Home</a>
    </div>
  <?php endif; ?>
</div>

</body>
</html>

<?php
session_start();
include '../config/db.php';

$user_id = (int) ($_SESSION['user']['id'] ?? 0);
$user_role = $_SESSION['user']['role'] ?? '';

if (!in_array($user_role, ['artist', 'admin'])) {
    die("Access denied");
}

// Get artwork ID from GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid artwork ID.");
}

$artist_id = null;
$artists = [];

if ($user_role === 'artist') {
    // Get artist_id for this user
    $result = $conn->query("SELECT id FROM artists WHERE user_id = $user_id");
    if ($result && $result->num_rows > 0) {
        $artist_id = (int) $result->fetch_assoc()['id'];
    } else {
        die("Artist profile not found.");
    }
} elseif ($user_role === 'admin') {
    // Admin can select artist from dropdown
    $artists_result = $conn->query("SELECT artists.id, users.name FROM artists JOIN users ON artists.user_id = users.id ORDER BY users.name");
    if ($artists_result) {
        while ($row = $artists_result->fetch_assoc()) {
            $artists[] = $row;
        }
    }
}

// Fetch existing artwork
if ($user_role === 'artist') {
    // Artist can only edit own artworks
    $stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ? AND artist_id = ?");
    $stmt->bind_param("ii", $id, $artist_id);
} else {
    // Admin can edit any artwork
    $stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
    $stmt->bind_param("i", $id);
}
$stmt->execute();
$result = $stmt->get_result();
$artwork = $result->fetch_assoc();
$stmt->close();

if (!$artwork) {
    die("Artwork not found or access denied.");
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($user_role === 'admin') {
        $artist_id = (int) ($_POST['artist_id'] ?? 0);
        if ($artist_id === 0) {
            $message = '<div class="alert alert-danger">Please select an artist.</div>';
        }
    }

    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $medium = trim($_POST['medium'] ?? '');
    $year_created = isset($_POST['year_created']) ? (int)$_POST['year_created'] : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $description = trim($_POST['description'] ?? '');

    if (!$message) {
        if (empty($title) || empty($category) || empty($medium)) {
            $message = '<div class="alert alert-danger">Please fill in all required fields.</div>';
        } elseif ($year_created < 0 || $year_created > (int)date('Y')) {
            $message = '<div class="alert alert-danger">Enter a valid year.</div>';
        } else {
            // Handle optional image upload
            $image_name = $artwork['image']; // keep existing image by default
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Validate image upload
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_size = $_FILES['image']['size'];
                $file_type = mime_content_type($file_tmp);

                if (!in_array($file_type, $allowed_types)) {
                    $message = '<div class="alert alert-danger">Only JPG, PNG, and GIF images are allowed.</div>';
                } elseif ($file_size > 5 * 1024 * 1024) {
                    $message = '<div class="alert alert-danger">File must be under 5MB.</div>';
                } else {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image_name = uniqid('art_', true) . '.' . $ext;
                    $upload_dir = __DIR__ . '/../uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $target = $upload_dir . $image_name;

                    if (!move_uploaded_file($file_tmp, $target)) {
                        $message = '<div class="alert alert-danger">Failed to upload image.</div>';
                    } else {
                        // Optionally delete old image file here
                        if ($artwork['image'] && file_exists($upload_dir . $artwork['image'])) {
                            @unlink($upload_dir . $artwork['image']);
                        }
                    }
                }
            }
        }
    }

    if (!$message) {
        // Update artwork record
        $stmt = $conn->prepare("UPDATE artworks SET artist_id = ?, title = ?, category = ?, medium = ?, year_created = ?, price = ?, description = ?, image = ? WHERE id = ?");
        $stmt->bind_param("isssiissi", $artist_id, $title, $category, $medium, $year_created, $price, $description, $image_name, $id);

        if ($stmt->execute()) {
            header("Location: manage_artworks.php?updated=1");
            exit;
        } else {
            $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    }
} else {
    // On GET populate form with existing data
    $_POST = [
        'artist_id' => $artwork['artist_id'],
        'title' => $artwork['title'],
        'category' => $artwork['category'],
        'medium' => $artwork['medium'],
        'year_created' => $artwork['year_created'],
        'price' => $artwork['price'],
        'description' => $artwork['description'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Artwork</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: url('https://www.transparenttextures.com/patterns/debut-light.png'), linear-gradient(135deg, #f0f4f8, #d9e2ec);
      background-attachment: fixed;
      min-height: 100vh;
    }
    .form-container {
      max-width: 650px;
      margin: 60px auto;
      background: white;
      padding: 35px 40px;
      border-radius: 12px;
      box-shadow: 0 0 18px rgba(0, 0, 0, 0.08);
    }
    h2 {
      text-align: center;
      color: #0056b3;
      font-weight: bold;
      margin-bottom: 25px;
    }
    .btn-back {
      margin-bottom: 20px;
    }
    .current-image {
      max-width: 100%;
      max-height: 300px;
      margin-bottom: 15px;
      display: block;
    }
  </style>
</head>
<body>
<div class="form-container">
  <a href="manage_artworks.php" class="btn btn-outline-secondary btn-sm btn-back">← Back</a>

  <h2>Edit Artwork</h2>

  <?= $message ?>

  <form method="POST" enctype="multipart/form-data" novalidate>
    <?php if ($user_role === 'admin'): ?>
      <div class="mb-3">
        <label for="artist_id" class="form-label">Select Artist <span class="text-danger">*</span></label>
        <select name="artist_id" id="artist_id" class="form-select" required>
          <option value="">-- Select Artist --</option>
          <?php foreach ($artists as $artist): ?>
            <option value="<?= $artist['id'] ?>" <?= (isset($_POST['artist_id']) && $_POST['artist_id'] == $artist['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($artist['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Title <span class="text-danger">*</span></label>
      <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" placeholder="Enter title">
    </div>

    <div class="mb-3">
      <label class="form-label">Category <span class="text-danger">*</span></label>
      <select name="category" id="category" class="form-select" required>
        <option value="">Select Category</option>
        <?php
        $categories = ['Painting', 'Sculpture', 'Photography', 'Drawing'];
        foreach ($categories as $cat) {
            $selected = ($_POST['category'] ?? '') === $cat ? 'selected' : '';
            echo "<option value=\"$cat\" $selected>$cat</option>";
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Medium <span class="text-danger">*</span></label>
      <select name="medium" id="medium" class="form-select" required></select>
    </div>

    <div class="mb-3">
      <label class="form-label">Year Created</label>
      <input type="number" name="year_created" class="form-control" min="0" max="<?= date('Y') ?>" value="<?= htmlspecialchars($_POST['year_created'] ?? '') ?>" placeholder="e.g. 2024">
    </div>

    <div class="mb-3">
      <label class="form-label">Price (KES)</label>
      <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" placeholder="e.g. 2500.00">
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" rows="4" class="form-control" placeholder="Describe your artwork"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Current Image</label><br>
      <?php if ($artwork['image'] && file_exists(__DIR__ . '/../uploads/' . $artwork['image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($artwork['image']) ?>" alt="Artwork Image" class="current-image" />
      <?php else: ?>
        <p>No image uploaded.</p>
      <?php endif; ?>
    </div>

    <div class="mb-4">
      <label class="form-label">Change Image (optional)</label>
      <input type="file" name="image" class="form-control" accept="image/*">
      <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 5MB. Leave blank to keep current image.</div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Update Artwork</button>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  const mediums = {
    Painting: ["Oil", "Acrylic", "Watercolor", "Ink", "Tempera"],
    Sculpture: ["Clay", "Wood", "Stone", "Metal", "Bronze"],
    Photography: ["Digital", "Black & White", "Color", "Polaroid"],
    Drawing: ["Pencil", "Charcoal", "Pastel", "Marker", "Graphite"]
  };

  function populateMediums(category, selected = '') {
    let options = '<option value="">Select Medium</option>';
    if (mediums[category]) {
      mediums[category].forEach(m => {
        options += `<option value="${m}"${m === selected ? ' selected' : ''}>${m}</option>`;
      });
    }
    $('#medium').html(options);
  }

  $(function () {
    const selectedCategory = $('#category').val();
    const selectedMedium = <?= json_encode($_POST['medium'] ?? '') ?>;
    if (selectedCategory) populateMediums(selectedCategory, selectedMedium);

    $('#category').on('change', function () {
      populateMediums(this.value);
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

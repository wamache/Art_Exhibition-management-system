<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    die("Access denied");
}

$user_id = $_SESSION['user']['id'];

// Get artist_id linked to user
$stmt = $conn->prepare("SELECT id FROM artists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$artist = $res->fetch_assoc();
$stmt->close();

if (!$artist) {
    die("Artist profile not found.");
}
$artist_id = (int)$artist['id'];

// Get artwork ID from URL and validate
$artwork_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($artwork_id <= 0) {
    die("Invalid artwork ID.");
}

// Fetch artwork if it belongs to this artist
$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ? AND artist_id = ?");
$stmt->bind_param("ii", $artwork_id, $artist_id);
$stmt->execute();
$res = $stmt->get_result();
$artwork = $res->fetch_assoc();
$stmt->close();

if (!$artwork) {
    die("Artwork not found or access denied.");
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $medium = trim($_POST['medium'] ?? '');
    $year_created = isset($_POST['year_created']) ? (int)$_POST['year_created'] : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $description = trim($_POST['description'] ?? '');

    // Validation
    if (empty($title)) {
        $message = '<div class="alert alert-danger">Title is required.</div>';
    } elseif (empty($category)) {
        $message = '<div class="alert alert-danger">Category is required.</div>';
    } elseif (empty($medium)) {
        $message = '<div class="alert alert-danger">Medium is required.</div>';
    } elseif ($year_created < 0 || $year_created > (int)date('Y')) {
        $message = '<div class="alert alert-danger">Enter a valid year.</div>';
    } else {
        // Handle image upload if provided
        $image_name = $artwork['image']; // current image

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_size = $_FILES['image']['size'];
            $file_type = mime_content_type($file_tmp);

            if (!in_array($file_type, $allowed_types)) {
                $message = '<div class="alert alert-danger">Only JPG, PNG, and GIF images are allowed.</div>';
            } elseif ($file_size > 5 * 1024 * 1024) {
                $message = '<div class="alert alert-danger">File size must be less than 5MB.</div>';
            } else {
                // Delete old image
                $old_path = __DIR__ . '/../uploads/' . $artwork['image'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }

                // Save new image
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = uniqid('artwork_', true) . '.' . $ext;
                $upload_dir = __DIR__ . '/../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $target = $upload_dir . $image_name;
                if (!move_uploaded_file($file_tmp, $target)) {
                    $message = '<div class="alert alert-danger">Failed to upload new image.</div>';
                }
            }
        }

        if (empty($message)) {
            // Update database record
            $stmt = $conn->prepare("UPDATE artworks SET title = ?, category = ?, medium = ?, year_created = ?, price = ?, description = ?, image = ? WHERE id = ? AND artist_id = ?");
            $stmt->bind_param("sssidsiii", $title, $category, $medium, $year_created, $price, $description, $image_name, $artwork_id, $artist_id);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: manage_artworks.php?updated=true");
                exit;
            } else {
                $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
                $stmt->close();
            }
        }
    }
}

// For repopulating form if POST failed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($message)) {
    $artwork['title'] = $_POST['title'] ?? $artwork['title'];
    $artwork['category'] = $_POST['category'] ?? $artwork['category'];
    $artwork['medium'] = $_POST['medium'] ?? $artwork['medium'];
    $artwork['year_created'] = $_POST['year_created'] ?? $artwork['year_created'];
    $artwork['price'] = $_POST['price'] ?? $artwork['price'];
    $artwork['description'] = $_POST['description'] ?? $artwork['description'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Artwork</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('https://www.transparenttextures.com/patterns/fabric-of-squares.png'), linear-gradient(120deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 700px;
            margin: 60px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 25px rgba(0,0,0,0.05);
        }
        h2 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <a href="manage_artworks.php" class="btn btn-outline-secondary back-btn">← Back to My Artworks</a>

    <h2>Edit Artwork</h2>

    <?= $message ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
            <input
                type="text"
                id="title"
                name="title"
                class="form-control"
                required
                value="<?= htmlspecialchars($artwork['title']) ?>"
                placeholder="Enter artwork title"
            >
        </div>

        <div class="mb-3">
            <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
            <select name="category" id="category" class="form-select" required>
                <option value="">Select Category</option>
                <?php
                $categories = ['Painting', 'Sculpture', 'Photography', 'Drawing'];
                foreach ($categories as $cat) {
                    $selected = ($artwork['category'] === $cat) ? 'selected' : '';
                    echo "<option value=\"$cat\" $selected>$cat</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="medium" class="form-label fw-semibold">Medium <span class="text-danger">*</span></label>
            <select name="medium" id="medium" class="form-select" required>
                <option value="">Select Medium</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="year_created" class="form-label">Year Created</label>
            <input
                type="number"
                id="year_created"
                name="year_created"
                class="form-control"
                min="0"
                max="<?= date('Y') ?>"
                value="<?= htmlspecialchars($artwork['year_created']) ?>"
                placeholder="e.g., 2023"
            >
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input
                type="number"
                id="price"
                name="price"
                class="form-control"
                step="0.01"
                min="0"
                value="<?= htmlspecialchars($artwork['price']) ?>"
                placeholder="e.g., 150.00"
            >
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="4"
                placeholder="Describe your artwork"
            ><?= htmlspecialchars($artwork['description']) ?></textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Current Image</label><br>
            <img src="../uploads/<?= htmlspecialchars($artwork['image']) ?>" alt="Artwork Image" style="max-width: 200px; border-radius: 5px; margin-bottom: 10px;">
        </div>

        <div class="mb-4">
            <label for="image" class="form-label fw-semibold">Upload New Image (optional)</label>
            <input
                class="form-control"
                type="file"
                id="image"
                name="image"
                accept="image/jpeg,image/png,image/gif"
            >
            <div class="form-text">Only JPG, PNG, GIF. Max size 5MB.</div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Artwork</button>
    </form>
</div>

<script>
    // Populate Medium dropdown based on Category selection
    const mediumMap = {
        'Painting': ['Oil', 'Acrylic', 'Watercolor', 'Mixed Media'],
        'Sculpture': ['Stone', 'Metal', 'Wood', 'Clay'],
        'Photography': ['Digital', 'Film', 'Black & White', 'Color'],
        'Drawing': ['Pencil', 'Ink', 'Charcoal', 'Pastel']
    };

    const categorySelect = document.getElementById('category');
    const mediumSelect = document.getElementById('medium');

    function populateMedium() {
        const selectedCat = categorySelect.value;
        mediumSelect.innerHTML = '<option value="">Select Medium</option>';
        if (mediumMap[selectedCat]) {
            mediumMap[selectedCat].forEach(m => {
                const option = document.createElement('option');
                option.value = m;
                option.textContent = m;
                if (m === '<?= addslashes($artwork['medium']) ?>') {
                    option.selected = true;
                }
                mediumSelect.appendChild(option);
            });
        }
    }

    categorySelect.addEventListener('change', populateMedium);

    // Initial population on page load
    populateMedium();
</script>

</body>
</html>

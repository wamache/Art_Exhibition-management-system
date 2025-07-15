<?php
<<<<<<< HEAD
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
=======
// include("includes/head.php");
// include("config/db.php"); // Make sure this file sets up your $conn
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['art_title']);
    $category = mysqli_real_escape_string($conn, $_POST['art_category']);
    $medium = mysqli_real_escape_string($conn, $_POST['art_medium']);
    $price = (float) $_POST['art_price'];
    $description = mysqli_real_escape_string($conn, $_POST['art_description']);

    $imageName = basename($_FILES["art_image"]["name"]);
    $targetDir = "pictures/arts/";
    $targetFile = $targetDir . $imageName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $uploadOk = 1;
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($imageFileType, $allowedTypes)) {
        $message = "<div class='alert alert-danger'>Only JPG, JPEG, PNG & GIF files are allowed.</div>";
        $uploadOk = 0;
    }
    $targetDir = "pictures/arts/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true); // creates folder if it doesn't exist
}

    if ($uploadOk && move_uploaded_file($_FILES["art_image"]["tmp_name"], $targetFile)) {
        $user_id = 1; // Replace with session user_id
        $status = 'Available';

        // $user_id = 1; // Replace with logged-in artist id from session

$insert_sql = "INSERT INTO artworks 
    (artist_id, title, category, medium, price, description, image, status) 
    VALUES 
    ('$user_id', '$title', '$category', '$medium', '$price', '$description', '$imageName', '$status')";


        // $insert_sql = "INSERT INTO artworks (art_title, art_category, art_medium, art_price, art_description, art_imagepath, art_status, user_id)
                      //  VALUES ('$title', '$category', '$medium', '$price', '$description', '$imageName', '$status', '$user_id')";


        if (mysqli_query($conn, $insert_sql)) {
            $message = "<div class='alert alert-success'>Artwork added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    } elseif ($uploadOk) {
        $message = "<div class='alert alert-danger'>Error uploading image.</div>";
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
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
=======
    <meta charset="UTF-8">
    <title>Add Artwork</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            font-family: "Yu Gothic UI Light";
        }

        .form-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2d70d5;
            font-variant: small-caps;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        .form-control, .form-select {
            box-shadow: none;
        }

        .btn-primary {
            background-color: #2d70d5;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #234a92;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Add New Artwork</h2>

        <?php if (isset($message)) echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="art_title">Artwork Title</label>
                <input type="text" name="art_title" id="art_title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="art_category">Category</label>
                <select name="art_category" id="art_category" class="form-select" required>
                    <option value="">Select Category</option>
                    <option value="Painting">Painting</option>
                    <option value="Sculpture">Sculpture</option>
                    <option value="Photography">Photography</option>
                    <option value="Drawing">Drawing</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="art_medium">Medium</label>
                <select name="art_medium" id="art_medium" class="form-select" required>
                    <option value="">Select Medium</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="art_price">Price</label>
                <input type="number" name="art_price" id="art_price" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="art_description">Description</label>
                <textarea name="art_description" id="art_description" rows="4" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label for="art_image">Artwork Image</label>
                <input type="file" name="art_image" id="art_image" class="form-control" accept="image/*" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary px-4">Add Artwork</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    const mediums = {
        Painting: ["Oil", "Acrylic", "Watercolor", "Ink", "Tempera"],
        Sculpture: ["Clay", "Wood", "Stone", "Metal", "Bronze"],
        Photography: ["Digital", "Black & White", "Color", "Polaroid"],
        Drawing: ["Pencil", "Charcoal", "Pastel", "Marker", "Graphite"]
    };

    $('#art_category').on('change', function () {
        let cat = $(this).val();
        let options = '<option value="">Select Medium</option>';

        if (mediums[cat]) {
            mediums[cat].forEach(function (med) {
                options += `<option value="${med}">${med}</option>`;
            });
        }

        $('#art_medium').html(options);
    });
</script>

>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7

</body>
</html>

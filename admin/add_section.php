<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO sections (title, content, image_url) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $image_url]);

    echo "Section added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Section</title>
    <style>
        form {
            max-width: 500px;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<form method="post">
    <label for="title">Title:</label><br>
    <input type="text" id="title" name="title" required><br>

    <label for="content">Content:</label><br>
    <textarea id="content" name="content" rows="4" required></textarea><br>

    <label for="image_url">Image URL (optional):</label><br>
    <input type="text" id="image_url" name="image_url"><br>

    <button type="submit">Add Section</button>
</form>

</body>
</html>

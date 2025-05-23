<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $bio = trim($_POST['bio']);
    $contact = trim($_POST['contact']);

    // Check if user already exists as artist
    $check = $conn->prepare("SELECT id FROM artists WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<p style='color:red;'>This user is already registered as an artist.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO artists (user_id, bio, contact) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $bio, $contact);
        if ($stmt->execute()) {
            header("Location: /project/art_exhibition/admin/manage_artists.php");
            exit;
        } else {
            echo "<p style='color:red;'>Error adding artist. Please try again.</p>";
        }
    }
    $check->close();
}
$users = $conn->query("SELECT * FROM users WHERE role = 'artist' ORDER BY name");
?>

<h2>Add New Artist</h2>
<form method="post" class="w-50 mx-auto">
    <div class="mb-3">
        <label for="user_id" class="form-label">User:</label>
        <select name="user_id" id="user_id" class="form-select" required>
            <option value="">-- Select User --</option>
            <?php while ($user = $users->fetch_assoc()): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="bio" class="form-label">Bio:</label>
        <textarea name="bio" id="bio" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
        <label for="contact" class="form-label">Contact:</label>
        <input type="text" name="contact" id="contact" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Artist</button>
</form>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
session_start();

// Ensure the user is logged in and is an artist
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    header("Location: /project/art_exhibition/artist/login.php"); // Redirect to login if not logged in or not an artist
    exit;
}

// Greeting message for the artist
echo "<h1>Welcome, " . htmlspecialchars($_SESSION['user']['username']) . "!</h1>";
?>

<ul>
    <!-- Link to manage profile -->
    <li><a href="/project/art_exhibition/artist/profile.php">Manage Profile</a></li>

    <!-- Link to manage artworks -->
    <li><a href="/project/art_exhibition/artist/manage_artworks.php">Manage Artworks</a></li>

    <!-- Link to view exhibitions the artist is part of -->
    <li><a href="/project/art_exhibition/artist/my_exhibitions.php">View My Exhibitions</a></li>
    <!-- <li><a href="/project/art_exhibition/artist/view_sales.php">sales</a></li> -->
    

    <!-- Link to submit artwork for exhibitions -->
    <li><a href="/project/art_exhibition/artist/submit_artwork.php">Submit Artwork to Exhibition</a></li>

    <!-- Link to view sales of the artist's artworks -->
    <li><a href="/project/art_exhibition/artist/view_sales.php">View Sales</a></li>

    <!-- Logout link -->
    <li><a href="/auth/logout.php">Logout</a></li>
</ul>

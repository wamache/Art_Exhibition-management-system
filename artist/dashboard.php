<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    header("Location: /project/art_exhibition/artist/login.php");
    exit;
}

$username = htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['user']['username'] ?? 'Artist');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Artist Dashboard</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
  body {
    min-height: 100vh;
    display: flex;
    background: #f5f7fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
  }
  .sidebar {
    width: 260px;
    background: #1f2937; /* dark slate gray */
    color: #cbd5e1; /* light slate */
    display: flex;
    flex-direction: column;
    padding: 1.5rem 1rem;
    transition: all 0.3s ease;
  }
  .sidebar .brand {
    font-size: 1.8rem;
    font-weight: 700;
    color: #38bdf8; /* bright cyan */
    margin-bottom: 2rem;
    user-select: none;
  }
  .nav-link {
    color: #cbd5e1;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s ease, color 0.2s ease;
  }
  .nav-link:hover {
    background-color: #2563eb; /* blue-600 */
    color: #e0e7ff; /* lighter text */
    text-decoration: none;
  }
  .nav-link.active {
    background-color: #3b82f6; /* blue-500 */
    color: #fff;
    font-weight: 600;
  }
  .nav-item.mt-auto a {
    color: #ef4444; /* red-500 */
    font-weight: 600;
  }
  .nav-item.mt-auto a:hover {
    color: #b91c1c; /* red-700 */
  }

  .content {
    flex-grow: 1;
    padding: 3rem 2rem;
    background: #ffffff;
    box-shadow: inset 0 0 30px rgb(0 0 0 / 0.05);
    min-height: 100vh;
  }

  .welcome-msg {
    font-size: 1.8rem;
    font-weight: 600;
    color: #334155; /* dark slate */
    margin-bottom: 2rem;
  }

  /* Responsive toggle button */
  .sidebar-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    background: #2563eb;
    color: white;
    border: none;
    padding: 0.4rem 0.8rem;
    font-size: 1.25rem;
    border-radius: 0.375rem;
    cursor: pointer;
    z-index: 1050;
  }

  @media (max-width: 768px) {
    body {
      flex-direction: column;
    }
    .sidebar {
      position: fixed;
      top: 0;
      left: -270px;
      height: 100vh;
      z-index: 1040;
      box-shadow: 2px 0 8px rgb(0 0 0 / 0.15);
    }
    .sidebar.show {
      left: 0;
      transition: left 0.3s ease;
    }
    .content {
      padding: 1rem;
      margin-top: 3rem;
    }
    .sidebar-toggle {
      display: block;
    }
  }
</style>
</head>
<body>

<button class="sidebar-toggle" aria-label="Toggle sidebar">&#9776;</button>

<nav class="sidebar">
  <div class="brand">ArtExhibit</div>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a href="/project/art_exhibition/artist/profile.php" class="nav-link">Manage Profile</a>
    </li>
    <li class="nav-item">
      <a href="/project/art_exhibition/artist/manage_artworks.php" class="nav-link">Manage Artworks</a>
    </li>
    <li class="nav-item">
      <a href="/project/art_exhibition/artist/my_exhibitions.php" class="nav-link">View My Exhibitions</a>
    </li>
    <li class="nav-item">
      <a href="/project/art_exhibition/artist/submit_artwork.php" class="nav-link">Submit Artwork to Exhibition</a>
    </li>
    <li class="nav-item">
      <a href="/project/art_exhibition/artist/view_sales.php" class="nav-link">View Sales</a>
    </li>
    <li class="nav-item mt-auto">
      <a href="/auth/logout.php" class="nav-link">Logout</a>
    </li>
  </ul>
</nav>

<div class="content">
  <h1 class="welcome-msg">Welcome, <?= $username ?>!</h1>
  <!-- Your page content here -->
</div>

<script>
  $(document).ready(function(){
    $('.sidebar-toggle').click(function() {
      $('.sidebar').toggleClass('show');
    });
  });
</script>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

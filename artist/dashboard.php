<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    header("Location: /project/art_exhibition/artist/login.php");
    exit;
}

$username = htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['user']['username'] ?? 'Artist');

$totalArtworks = 12;
$upcomingExhibitions = 3;
$totalSales = 1250.00;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Artist Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      min-height: 100vh;
      display: flex;
      margin: 0;
      background: url('https://www.transparenttextures.com/patterns/debut-light.png'), linear-gradient(135deg, #f5f7fa, #e0e7ff);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .sidebar {
      width: 260px;
      background: #1f2937;
      color: #cbd5e1;
      display: flex;
      flex-direction: column;
      padding: 1.5rem 1rem;
      transition: all 0.3s ease;
    }

    .sidebar .brand {
      font-size: 1.8rem;
      font-weight: 700;
      color: #38bdf8;
      margin-bottom: 2rem;
    }

    .nav-link {
      color: #cbd5e1;
      padding: 0.75rem 1rem;
      border-radius: 0.375rem;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    .nav-link:hover {
      background-color: #2563eb;
      color: #e0e7ff;
    }

    .nav-link.active {
      background-color: #3b82f6;
      color: #fff;
      font-weight: 600;
    }

    .nav-item.mt-auto a {
      color: #ef4444;
      font-weight: 600;
    }

    .nav-item.mt-auto a:hover {
      color: #b91c1c;
    }

    .content {
      flex-grow: 1;
      padding: 3rem 2rem;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: inset 0 0 30px rgb(0 0 0 / 0.05);
      min-height: 100vh;
    }

    .welcome-msg {
      font-size: 1.8rem;
      font-weight: 600;
      color: #334155;
      margin-bottom: 2rem;
    }

    .quote {
      font-style: italic;
      font-size: 1.2rem;
      color: #555;
      margin-bottom: 2rem;
    }

    .featured {
      background-color: #f1f5f9;
      border-left: 4px solid #3b82f6;
      padding: 1rem;
      margin-bottom: 2rem;
      border-radius: 0.375rem;
    }

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
  <div class="brand">AEMS</div>
  <ul class="nav flex-column">
    <li class="nav-item"><a href="/projects/art_exhibition/artist/profile.php" class="nav-link">Manage Profile</a></li>
    <li class="nav-item"><a href="/projects/art_exhibition/artist/manage_artworks.php" class="nav-link">Manage Artworks</a></li>
    <li class="nav-item"><a href="/projects/art_exhibition/artist/my_exhibitions.php" class="nav-link">View My Exhibitions</a></li>
    <li class="nav-item"><a href="/projects/art_exhibition/artist/submit_artwork.php" class="nav-link">Submit Artwork</a></li>
    <li class="nav-item"><a href="/projects/art_exhibition/artist/view_sales.php" class="nav-link">View Sales</a></li>
    <li class="nav-item mt-auto"><a href="../logout.php" class="nav-link">Logout</a></li>
  </ul>
</nav>

<div class="content">
  <h1 class="welcome-msg">Welcome, <?= $username ?>!</h1>
  <p class="quote">“Every artist was first an amateur.” – Ralph Waldo Emerson</p>

  <div class="row mb-4">
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Total Artworks</h5>
          <p class="card-text fs-4 fw-semibold text-primary"><?= $totalArtworks ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Upcoming Exhibitions</h5>
          <p class="card-text fs-4 fw-semibold text-success"><?= $upcomingExhibitions ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Total Sales</h5>
          <p class="card-text fs-4 fw-semibold text-danger">$<?= number_format($totalSales, 2) ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="featured">
    <h5>Featured Tip</h5>
    <p>Don't forget to update your profile with new artwork. Fresh content attracts more attention!</p>
  </div>

  <div class="featured">
    <h5>Recent Activity</h5>
    <ul class="mb-0">
      <li>“Sunset Dream” was added to your gallery.</li>
      <li>You submitted 2 works to the "Autumn Art Gala".</li>
      <li>You earned $400 in sales last week.</li>
    </ul>
  </div>
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

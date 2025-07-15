<?php
// No session check — this is the public admin landing page
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Portal | Art Exhibition Platform</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    .hero {
      background: linear-gradient(rgba(44, 62, 80, 0.7), rgba(44, 62, 80, 0.7)),
                  url('https://www.workbc.ca/sites/default/files/styles/hero_image/public/2025-06/NOC%2050010-1206870317.jpg?itok=6qWiHSDy') no-repeat center center/cover;
      height: 100vh;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      text-align: center;
      padding: 20px;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 1.25rem;
      max-width: 600px;
    }

    .login-btn {
      margin-top: 30px;
    }

    footer {
      background-color: #2c3e50;
      color: #ecf0f1;
      padding: 15px 0;
      text-align: center;
    }

    footer a {
      color: #bdc3c7;
      text-decoration: none;
    }
  </style>
</head>
<body>

<!-- Hero Section -->
<section class="hero">
  <h1>Admin Dashboard Access</h1>
  <p>Welcome to the administration portal for the Art Exhibition Management System.</p>
  <div class="login-btn">
    <a href="login.php" class="btn btn-light btn-lg">Admin Login</a>
  </div>
</section>

<!-- Footer -->
<footer>
  <div class="container">
    <p>&copy; <?= date('Y') ?> Art Exhibition Platform — Admin Portal</p>
    <small><a href="mailto:support@artexhibition.com">support@artexhibition.com</a></small>
  </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

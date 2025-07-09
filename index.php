<?php
require_once 'config/db.php';

// Fetch exhibitions from the DB
$sql = "SELECT * FROM exhibitions ORDER BY start_date DESC LIMIT 6";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AEMS - View Art Exhibitions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    .hero {
      background: url('banner.jpg') no-repeat center center/cover;
      color: white;
      padding: 100px 0;
      text-align: center;
    }
    .section {
      padding: 60px 0;
    }
    .feature-icon {
      font-size: 3rem;
    }
    footer {
      background: #222;
      color: white;
      padding: 40px 0;
    }
  </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">AEMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="exhibition.php">Exhibitions</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="artist/index.php">Artists/Curators</a></li>
        <li class="nav-item"><a class="nav-link" href="contact-us.php">Pricing</a></li>
        <li class="nav-item"><a class="nav-link" href="contact-us.php">Contact Us</a></li>
        <li class="nav-item"><a class="nav-link" href="about-us.php">About Us</a></li>
      </ul>
      <a href="visitor/visitor_login.php" class="btn btn-outline-light me-2">Login</a>
      <a href="visitor/visitor_register.php" class="btn btn-primary">Signup</a>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<header class="hero d-flex align-items-center text-white"
        style="background: url('https://images.unsplash.com/photo-1526948128573-703ee1aeb6fa?auto=format&fit=crop&w=1950&q=80') no-repeat center center/cover; min-height: 100vh; position: relative;">
  <div style="position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.5);"></div>
  <div class="container text-center position-relative z-1">
    <h1 class="display-3 fw-bold mb-4">Explore Art Exhibitions Seamlessly</h1>
    <p class="lead mb-5">From curation to ticketing—one platform for everything art.</p>
    <a href="#" class="btn btn-lg btn-light me-3 shadow">Get Started</a>
    <a href="explore_artworks.php" class="btn btn-lg btn-outline-light shadow">Explore Exhibitions</a>
  </div>
</header>

<!-- Featured Exhibitions -->
<section class="section bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Featured Exhibitions</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($exhibit = $result->fetch_assoc()): ?>
          <div class="col">
            <div class="card h-100">
              <img src="uploads/<?= htmlspecialchars($exhibit['image'] ?: 'default.jpg') ?>"
                   class="card-img-top"
                   alt="<?= htmlspecialchars($exhibit['title']) ?>" />
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($exhibit['title']) ?></h5>
                <p class="card-text">
                  <?= date("M j", strtotime($exhibit['start_date'])) ?> –
                  <?= date("M j, Y", strtotime($exhibit['end_date'])) ?>,
                  <?= htmlspecialchars($exhibit['location']) ?>
                </p>
                <a href="exhibition.php?id=<?= $exhibit['id'] ?>" class="btn btn-outline-primary">View Details</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No exhibitions found.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- User Segments -->
<section class="section">
  <div class="container">
    <h2 class="text-center mb-5">For Every Art Enthusiast</h2>
    <div class="row text-center">
      <div class="col-md-3">
        <h4>🎨 Artists</h4>
        <p>Submit work, manage your portfolio</p>
      </div>
      <div class="col-md-3">
        <h4>🧠 Curators</h4>
        <p>Plan and organize exhibitions</p>
      </div>
      <div class="col-md-3">
        <h4>🎟️ Visitors</h4>
        <p>Book tickets, view exhibitions</p>
      </div>
      <div class="col-md-3">
        <h4>🏛️ Gallery Owners</h4>
        <p>Manage space, schedule events</p>
      </div>
    </div>
  </div>
</section>

<!-- Key Features -->
<section class="section bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Key Features</h2>
    <div class="row text-center">
      <div class="col-md-4"><div class="mb-3 feature-icon">📅</div><h5>Exhibition Scheduling</h5></div>
      <div class="col-md-4"><div class="mb-3 feature-icon">🖼️</div><h5>Artwork Cataloging</h5></div>
      <div class="col-md-4"><div class="mb-3 feature-icon">🎫</div><h5>Ticketing & Visitor Registration</h5></div>
      <div class="col-md-4"><div class="mb-3 feature-icon">🧑‍🎨</div><h5>Artist/Curator Management</h5></div>
      <div class="col-md-4"><div class="mb-3 feature-icon">📊</div><h5>Analytics & Reporting</h5></div>
      <div class="col-md-4"><div class="mb-3 feature-icon">🕶️</div><h5>Digital Galleries & VR Tours</h5></div>
    </div>
  </div>
</section>

<!-- Integrations -->
<section class="section">
  <div class="container text-center">
    <h2 class="mb-4">Integrations & Tools</h2>
    <p>Seamless connection with Stripe, Google Calendar, Mailchimp, and more.</p>
  </div>
</section>

<!-- Testimonials -->
<section class="section bg-light">
  <div class="container">
    <h2 class="text-center mb-5">What People Say</h2>
    <div class="row">
      <div class="col-md-6">
        <blockquote class="blockquote">
          <p>“ArtExhibitPro transformed the way we organize exhibitions.”</p>
          <footer class="blockquote-footer">Jane Doe, Curator at ModernArt NYC</footer>
        </blockquote>
      </div>
      <div class="col-md-6">
        <blockquote class="blockquote">
          <p>“An essential platform for artists wanting global exposure.”</p>
          <footer class="blockquote-footer">Alex Rivera, Contemporary Artist</footer>
        </blockquote>
      </div>
    </div>
  </div>
</section>

<!-- Gallery Preview -->
<section class="section text-center">
  <div class="container">
    <h2 class="mb-5">Gallery Preview</h2>
    <div class="row g-3">
      <div class="col-md-4"><img src="gallery1.jpg" class="img-fluid rounded" /></div>
      <div class="col-md-
      <div class="col-md-4"><img src="gallery2.jpg" class="img-fluid rounded" /></div>
      <div class="col-md-4"><img src="gallery3.jpg" class="img-fluid rounded" /></div>
    </div>
  </div>
</section>

<!-- Blog / News -->
<section class="section bg-light">
  <div class="container">
    <h2 class="text-center mb-5">News & Updates</h2>
    <div class="row">
      <div class="col-md-4">
        <h5>📰 Summer Curation Tips</h5>
        <p>Learn how to attract more visitors with seasonal art themes.</p>
      </div>
      <div class="col-md-4">
        <h5>🖼️ Artist Spotlight: Maya Chen</h5>
        <p>Discover the rising talent in minimal abstract expressionism.</p>
      </div>
      <div class="col-md-4">
        <h5>🎨 Upcoming Global Exhibits</h5>
        <p>Preview the top must-see exhibitions worldwide this fall.</p>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h5>Contact Us</h5>
        <form action="contact_process.php" method="POST">
          <div class="mb-2">
            <input type="email" class="form-control" name="email" placeholder="Your email" required />
          </div>
          <div class="mb-2">
            <textarea class="form-control" name="message" rows="3" placeholder="Your message" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
      <div class="col-md-6">
        <h5>Follow Us</h5>
        <p>
          <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
        </p>
        <p class="mt-3">© <?= date("Y") ?> AEMS. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

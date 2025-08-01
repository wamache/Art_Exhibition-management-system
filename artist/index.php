<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Welcome Artists | Art Exhibition Management</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap');

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background:
        url('https://www.transparenttextures.com/patterns/diamond-upholstery.png'),
        #f8f8f8;
      color: #333;
      min-height: 100vh;
    }
    header {
      background-color: #b22222;
      color: white;
      padding: 40px 20px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(178,34,34,0.3);
    }
    header h1 {
      margin: 0 0 8px;
      font-weight: 700;
      font-size: 2.8rem;
      letter-spacing: 1.2px;
    }
    header p {
      font-size: 1.2rem;
      font-weight: 500;
      opacity: 0.9;
      margin: 0;
    }
    .container {
      max-width: 900px;
      margin: 50px auto;
      padding: 30px 40px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    h2 {
      color: #b22222;
      font-weight: 700;
      margin-bottom: 20px;
      border-bottom: 2px solid #b22222;
      padding-bottom: 6px;
    }
    p {
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    .buttons {
      margin-bottom: 40px;
    }
    .buttons a {
      display: inline-block;
      padding: 14px 28px;
      background-color: #b22222;
      color: white;
      text-decoration: none;
      margin-right: 20px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 1.1rem;
      box-shadow: 0 4px 10px rgba(178,34,34,0.25);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .buttons a:hover {
      background-color: #8b1a1a;
      transform: scale(1.05);
    }
    ul.tips {
      list-style: square inside;
      padding-left: 0;
      font-size: 1.1rem;
      color: #444;
    }
    ul.tips li {
      margin-bottom: 12px;
      line-height: 1.5;
    }
  </style>
</head>
<body>

<header>
  <h1>Welcome, Artists!</h1>
  <p>Join our community and bring your art to the world.</p>
</header>

<div class="container">
  <h2>Get Started</h2>
  <p>To submit your work and participate in exhibitions, please register or log in.</p>

  <div class="buttons">
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
  </div>

  <h2>Tips for a Successful Exhibition</h2>
  <ul class="tips">
    <li>Choose your best and most representative pieces to showcase.</li>
    <li>Include a short artist statement that tells your story.</li>
    <li>Use high-quality images for online submissions.</li>
    <li>Make sure your artwork is properly labeled and prepared for display.</li>
    <li>Engage with visitors and other artists during the exhibition.</li>
  </ul>
</div>

</body>
</html>

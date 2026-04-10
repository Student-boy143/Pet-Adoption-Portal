<?php
// about.php
?>

<!doctype html>
<html lang="en">
<head>
  <title>About — FurEver Home</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>

  <link rel="stylesheet" href="styles/global.css" />
  <link rel="stylesheet" href="styles/header.css" />
  <link rel="stylesheet" href="styles/about.css" />
</head>

<body>

<div class="container">

  <!-- HEADER -->
  <header class="header">
    <div class="header-left">
      <div class="logo-circle">🐾</div>
      <h1 class="site-title">FurEver Home</h1>
    </div>

    <nav class="header-right">
      <a href="index.php" class="nav-link">Home</a>
      <a href="adopt.php" class="nav-link">Adopt</a>
      <a href="about.php" class="nav-link active">About</a>
      <a href="login.php" class="btn btn-ghost">Sign in</a>
      <a href="register.php" class="btn btn-primary">Register</a>
    </nav>

    <button class="hamburger" onclick="toggleMenu()">☰</button>
  </header>

  <!-- ABOUT HERO -->
  <section class="about-hero">
    <div class="about-hero-content">
      <span class="hero-badge">🐾 Our Story</span>
      <h1>We Believe Every Pet<br><em>Deserves a Home</em></h1>
      <p>
        FurEver Home was born from a simple belief: no animal should be left
        without love. We connect loving families with animals who need them
        most.
      </p>
    </div>
  </section>

  <!-- MISSION -->
  <section class="mission-section">
    <div class="mission-grid">
      <div class="mission-text">
        <h2>Our <span class="accent">Mission</span></h2>
        <p>
          We're on a mission to make pet adoption accessible, transparent,
          and joyful for everyone in India.
        </p>
      </div>
    </div>
  </section>

  <!-- VALUES -->
  <section class="values-section">
    <div class="section-header">
      <h2>What We Stand For</h2>
      <p>Our core values guide everything we do.</p>
    </div>
  </section>

  <!-- TEAM -->
  <section class="team-section">
    <div class="section-header">
      <h2>Meet the Team</h2>
    </div>
  </section>

  <!-- JOIN CTA -->
  <section class="join-section">
    <div class="join-content">
      <h2>Ready to Make a Difference?</h2>

      <div class="join-cta">
        <a href="adopt.php" class="btn btn-primary btn-lg">Adopt a Pet 🐾</a>
        <a href="register.php" class="btn btn-outline btn-lg">Join as Rescuer</a>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-bottom">
      <p>© 2026 FurEver Home. Made with ❤️ for animals everywhere.</p>
    </div>
  </footer>

</div>

<script>
function toggleMenu() {
  document.querySelector(".header-right").classList.toggle("open");
}
</script>

</body>
</html>
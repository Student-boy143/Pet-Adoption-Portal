<?php
// index.php
session_start();

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

$loggedIn    = isset($_SESSION['user_id']);
$firstname   = htmlspecialchars($_SESSION['firstname'] ?? '');
$role = $_SESSION['role'] ?? '';
?>
<!doctype html>
<html lang="en">
  <head>
    <title>FurEver Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles/global.css" />
    <link rel="stylesheet" href="styles/header.css" />
    <link rel="stylesheet" href="styles/main.css" />
    <style>
      .reveal { opacity:0; transform:translateY(32px); transition:opacity .6s ease,transform .6s ease; }
      .reveal.visible { opacity:1; transform:none; }
      .reveal-group > * { opacity:0; transform:translateY(24px); transition:opacity .5s ease,transform .5s ease; }
      .reveal-group.visible > *:nth-child(1){opacity:1;transform:none;transition-delay:.05s}
      .reveal-group.visible > *:nth-child(2){opacity:1;transform:none;transition-delay:.15s}
      .reveal-group.visible > *:nth-child(3){opacity:1;transform:none;transition-delay:.25s}
      .reveal-group.visible > *:nth-child(4){opacity:1;transform:none;transition-delay:.35s}
      .reveal-group.visible > *:nth-child(5){opacity:1;transform:none;transition-delay:.45s}
      .reveal-group.visible > *:nth-child(6){opacity:1;transform:none;transition-delay:.55s}
      .hero-content{animation:heroIn .8s ease both}
      .hero-visual{animation:heroIn .8s .2s ease both}
      @keyframes heroIn{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:none}}
      .pet-photo-wrap{overflow:hidden;height:180px}
      .pet-photo{width:100%;height:180px;object-fit:cover;display:block;transition:transform .4s ease}
      .pet-card{overflow:hidden}
      .pet-card:hover .pet-photo{transform:scale(1.05)}
      .hero-card{padding:0;overflow:hidden;gap:0}
      .hero-card img{width:100%;height:110px;object-fit:cover;transition:transform .4s ease}
      .hero-card:hover img{transform:scale(1.08)}
      .hero-card-label{padding:8px 10px;font-size:.78rem;font-weight:700;color:var(--muted);text-align:center}
      .t-avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;margin-bottom:10px}

      /* success toast */
      .toast {
        position: fixed; top: 80px; right: 24px; z-index: 999;
        background: #f0fff4; border: 1.5px solid #38a169; color: #276749;
        padding: 12px 20px; border-radius: 12px; font-size: .9rem;
        font-weight: 600; box-shadow: 0 4px 16px rgba(0,0,0,.1);
        animation: toastIn .4s ease;
      }
      @keyframes toastIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:none} }

      /* user greeting in header */
      .user-greeting {
        font-size: .9rem; font-weight: 700;
        color: var(--brown); padding: 8px 14px;
      }
    </style>
  </head>
  <body>
    <?php if ($success): ?>
      <div class="toast" id="toast">✅ <?= htmlspecialchars($success) ?></div>
      <script>setTimeout(() => document.getElementById('toast')?.remove(), 3500);</script>
    <?php endif; ?>

    <div class="container">
      <!-- HEADER -->
      <header class="header">
        <div class="header-left">
          <div class="logo-circle">🐾</div>
          <h1 class="site-title">FurEver Home</h1>
        </div>
        <nav class="header-right">
          <a href="index.php"  class="nav-link active">Home</a>
          <a href="adopt.php"  class="nav-link">Adopt</a>
          <a href="about.php"  class="nav-link">About</a>
          <?php if ($loggedIn): ?>

          <!-- SHOW ONLY FOR BUYER -->
          <?php if ($role === 'buyer'): ?>
            <a href="buyer/my-requests.php" class="nav-link">My Requests</a>
          <?php endif; ?>

          <span class="user-greeting">Hi, <?= $firstname ?>! 👋</span>

          <!-- SHOW ONLY FOR SELLER -->
          <?php if ($role === 'seller'): ?>
            <a href="seller/add-pet.php" class="btn btn-ghost">+ List a Pet</a>
          <?php endif; ?>

          <a href="actions/logout.php" class="btn btn-ghost">Logout</a>

        <?php else: ?>
            <a href="login.php"    class="btn btn-ghost">Sign in</a>
            <a href="register.php" class="btn btn-primary">Register</a>
          <?php endif; ?>
        </nav>
        <button class="hamburger" onclick="toggleMenu()">☰</button>
      </header>

      <!-- HERO -->
      <section class="hero">
        <div class="hero-content">
          <span class="hero-badge">🐶 500+ Pets Waiting</span>
          <h1 class="hero-title">Find Your<br /><em>FurEver</em> Friend</h1>
          <p class="hero-sub">Give a loving pet the forever home they deserve. Browse verified listings and connect directly with rescuers near you.</p>
          <div class="hero-cta">
            <a href="adopt.php" class="btn btn-primary btn-lg">Browse Pets 🐾</a>
            <a href="about.php" class="btn btn-outline btn-lg">Learn More</a>
          </div>
        </div>
        <div class="hero-visual">
          <div class="hero-card hero-card-1">
            <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=300&q=80" alt="Dog" />
            <div class="hero-card-label">Max, 3y · Labrador</div>
          </div>
          <div class="hero-card hero-card-2">
            <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=300&q=80" alt="Cat" />
            <div class="hero-card-label">Luna, 2y · Persian</div>
          </div>
          <div class="hero-card hero-card-3">
            <img src="https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?w=300&q=80" alt="Rabbit" />
            <div class="hero-card-label">Coco, 1y · Lop</div>
          </div>
          <div class="hero-card hero-card-4">
            <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=300&q=80" alt="Dog" />
            <div class="hero-card-label">Bella, 4y · Poodle</div>
          </div>
        </div>
      </section>

      <!-- STATS BAR -->
      <div class="stats-bar">
        <div class="stat"><strong data-target="1240" class="counter">0</strong><span>Pets Adopted</span></div>
        <div class="stat-divider"></div>
        <div class="stat"><strong data-target="320" class="counter">0</strong><span>Rescue Partners</span></div>
        <div class="stat-divider"></div>
        <div class="stat"><strong data-target="50" class="counter">0</strong><span>Cities Covered</span></div>
        <div class="stat-divider"></div>
        <div class="stat"><strong data-target="98" class="counter">0</strong><span>% Happy Families</span></div>
      </div>

      <!-- WHY SECTION -->
      <section class="why-section">
        <div class="section-header reveal">
          <h2>Why Choose <span class="accent">FurEver Home?</span></h2>
          <p>We make pet adoption simple, safe, and joyful.</p>
        </div>
        <div class="features-grid reveal-group">
          <div class="feature-card"><div class="feature-icon">❤️</div><h3>Trusted & Verified</h3><p>Every listing is reviewed and verified by our team for safety and accuracy.</p></div>
          <div class="feature-card"><div class="feature-icon">🏡</div><h3>Second Chances</h3><p>Give a rescued pet the loving home they've always needed.</p></div>
          <div class="feature-card"><div class="feature-icon">🔍</div><h3>Smart Search</h3><p>Filter by breed, age, size, and location to find your perfect match fast.</p></div>
          <div class="feature-card"><div class="feature-icon">🤝</div><h3>Direct Connect</h3><p>Chat directly with rescuers and shelters — no middlemen.</p></div>
          <div class="feature-card"><div class="feature-icon">🐶</div><h3>All Pets Welcome</h3><p>Dogs, cats, rabbits, birds and more — every kind of furry friend.</p></div>
          <div class="feature-card"><div class="feature-icon">💬</div><h3>Ongoing Support</h3><p>Our community and guides help you settle in with your new companion.</p></div>
        </div>
      </section>

      <!-- FEATURED PETS -->
      <section class="featured-section">
        <div class="section-header reveal">
          <h2>🐾 Featured Pets</h2>
          <p>These adorable animals are ready to meet you!</p>
        </div>
        <div class="pets-grid reveal-group">
          <div class="pet-card">
            <div class="pet-photo-wrap"><img class="pet-photo" src="https://images.unsplash.com/photo-1561037404-61cd46aa615b?w=400&q=80" alt="Bruno" /></div>
            <div class="pet-info"><h3>Bruno</h3><p class="pet-meta">Labrador · 2 years · Male</p><p class="pet-location">📍 Mumbai</p><a href="adopt.php" class="btn btn-primary btn-sm">Adopt Me</a></div>
          </div>
          <div class="pet-card">
            <div class="pet-photo-wrap"><img class="pet-photo" src="https://images.unsplash.com/photo-1596854407944-bf87f6fdd49e?w=400&q=80" alt="Mochi" /></div>
            <div class="pet-info"><h3>Mochi</h3><p class="pet-meta">Persian · 1 year · Female</p><p class="pet-location">📍 Pune</p><a href="adopt.php" class="btn btn-primary btn-sm">Adopt Me</a></div>
          </div>
          <div class="pet-card">
            <div class="pet-photo-wrap"><img class="pet-photo" src="https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?w=400&q=80" alt="Oreo" /></div>
            <div class="pet-info"><h3>Oreo</h3><p class="pet-meta">Holland Lop · 8 months · Male</p><p class="pet-location">📍 Delhi</p><a href="adopt.php" class="btn btn-primary btn-sm">Adopt Me</a></div>
          </div>
          <div class="pet-card">
            <div class="pet-photo-wrap"><img class="pet-photo" src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=400&q=80" alt="Daisy" /></div>
            <div class="pet-info"><h3>Daisy</h3><p class="pet-meta">Poodle · 3 years · Female</p><p class="pet-location">📍 Bengaluru</p><a href="adopt.php" class="btn btn-primary btn-sm">Adopt Me</a></div>
          </div>
        </div>
        <div class="center-cta reveal"><a href="adopt.php" class="btn btn-outline btn-lg">View All Pets →</a></div>
      </section>

      <!-- HOW IT WORKS -->
      <section class="how-section">
        <div class="section-header reveal"><h2>How It Works</h2><p>Three simple steps to find your forever companion.</p></div>
        <div class="steps-row reveal-group">
          <div class="step"><div class="step-num">01</div><h3>Browse & Search</h3><p>Explore hundreds of pets by breed, age, location and more.</p></div>
          <div class="step-arrow">→</div>
          <div class="step"><div class="step-num">02</div><h3>Connect & Meet</h3><p>Message the rescuer and arrange a meet-and-greet.</p></div>
          <div class="step-arrow">→</div>
          <div class="step"><div class="step-num">03</div><h3>Adopt & Love</h3><p>Complete the adoption and welcome your new family member home!</p></div>
        </div>
      </section>

      <!-- TESTIMONIALS -->
      <section class="testimonials-section">
        <div class="section-header reveal"><h2>💛 Happy Families</h2><p>Thousands of pets have found their forever homes through us.</p></div>
        <div class="testimonials-grid reveal-group">
          <div class="testimonial-card">
            <img class="t-avatar" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&q=80" alt="Priya" />
            <p>"We found our golden retriever Max on FurEver Home. The process was so smooth and the rescuer was amazing!"</p>
            <div class="testimonial-author">— Priya S., Mumbai</div>
          </div>
          <div class="testimonial-card">
            <img class="t-avatar" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&q=80" alt="Arjun" />
            <p>"Adopted two cats last year. The support team was incredible — they helped us every step of the way."</p>
            <div class="testimonial-author">— Arjun M., Bengaluru</div>
          </div>
          <div class="testimonial-card">
            <img class="t-avatar" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&q=80" alt="Deepa" />
            <p>"As a rescuer, this platform helped me find loving homes for 40+ animals. It's a blessing!"</p>
            <div class="testimonial-author">— Deepa R., Chennai</div>
          </div>
        </div>
      </section>

      <!-- FOOTER -->
      <footer class="footer">
        <div class="footer-top">
          <div class="footer-brand">
            <div class="logo-circle">🐾</div>
            <h2>FurEver Home</h2>
            <p>Connecting loving hearts with furry friends since 2024.</p>
          </div>
          <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="index.php">Home</a><a href="adopt.php">Adopt a Pet</a>
            <a href="about.php">About Us</a><a href="register.php">Register</a>
          </div>
          <div class="footer-links">
            <h4>Pets</h4>
            <a href="adopt.php">Dogs</a><a href="adopt.php">Cats</a>
            <a href="adopt.php">Rabbits</a><a href="adopt.php">Birds</a>
          </div>
          <div class="footer-links">
            <h4>Contact</h4>
            <a href="#">hello@fureverhome.in</a>
            <a href="#">+91 98765 43210</a>
          </div>
        </div>
        <div class="footer-bottom"><p>© 2024 FurEver Home. Made with ❤️ for animals everywhere.</p></div>
      </footer>
    </div>

    <script>
      function toggleMenu() { document.querySelector('.header-right').classList.toggle('open'); }

      const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
      }, { threshold: 0.15 });
      document.querySelectorAll('.reveal, .reveal-group').forEach(el => observer.observe(el));

      function animateCounter(el) {
        const target = parseInt(el.dataset.target);
        const suffix = '+';
        const step = target / (1800 / 16);
        let cur = 0;
        const tick = () => {
          cur = Math.min(cur + step, target);
          el.textContent = Math.floor(cur).toLocaleString() + (cur >= target ? suffix : '');
          if (cur < target) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
      }
      const statsObs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.querySelectorAll('.counter').forEach(animateCounter); statsObs.unobserve(e.target); } });
      }, { threshold: 0.5 });
      const bar = document.querySelector('.stats-bar');
      if (bar) statsObs.observe(bar);
    </script>
  </body>
</html>
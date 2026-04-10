<?php
// adopt.php
session_start();
require_once 'includes/db.php';

$loggedIn  = isset($_SESSION['user_id']);
$firstname = htmlspecialchars($_SESSION['firstname'] ?? '');
$accountType = $_SESSION['account_type'] ?? 'adopt';

//  Read filter inputs (GET params) 
$type   = $_GET['type']   ?? 'all';
$age    = $_GET['age']    ?? 'all';
$gender = $_GET['gender'] ?? 'all';
$city   = $_GET['city']   ?? 'all';
$sort   = $_GET['sort']   ?? 'newest';

//  Build SQL dynamically 
$where  = ["status = 'available'"];
$params = [];

if ($type !== 'all' && in_array($type, ['dog','cat','rabbit','bird','other'])) {
    $where[]  = 'type = ?';
    $params[] = $type;
}

if ($age !== 'all') {
    if ($age === 'young')  { $where[] = 'age_years < 2';              }
    if ($age === 'adult')  { $where[] = 'age_years BETWEEN 2 AND 5';  }
    if ($age === 'senior') { $where[] = 'age_years > 5';              }
}

if ($gender !== 'all' && in_array($gender, ['male','female'])) {
    $where[]  = 'gender = ?';
    $params[] = $gender;
}

if ($city !== 'all') {
    $where[]  = 'city = ?';
    $params[] = $city;
}

$orderBy = match($sort) {
    'oldest'    => 'created_at ASC',
    'name-asc'  => 'name ASC',
    'name-desc' => 'name DESC',
    default     => 'created_at DESC',
};

$sql  = 'SELECT * FROM pets';
$sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= " ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pets = $stmt->fetchAll();
$count = count($pets);

// Helper: age group label 
function ageLabel(float $y): string {
    if ($y < 1) return round($y * 12) . ' months';
    return $y == 1 ? '1 year' : $y . ' years';
}

// Sanitise current filter values for HTML
$fType   = htmlspecialchars($type);
$fAge    = htmlspecialchars($age);
$fGender = htmlspecialchars($gender);
$fCity   = htmlspecialchars($city);
$fSort   = htmlspecialchars($sort);

function sel(string $val, string $current): string {
    return $val === $current ? 'selected' : '';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Adopt a Pet — FurEver Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles/global.css" />
    <link rel="stylesheet" href="styles/header.css" />
    <link rel="stylesheet" href="styles/adopt.css" />
    <style>
      .listing-img { height:200px; padding:0; overflow:hidden; display:block; }
      .listing-img img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .4s ease; }
      .listing-card:hover .listing-img img { transform:scale(1.06); }
      .empty-state { grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--muted); }
      .empty-state .empty-emoji { font-size:3.5rem; display:block; margin-bottom:14px; }
      .empty-state h3 { font-family:var(--font-display); color:var(--brown); margin-bottom:8px; }
      .listing-card { animation:cardIn .35s ease both; }
      @keyframes cardIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none} }
      #result-count { font-size:.85rem; background:var(--warm); color:var(--brown); padding:4px 12px; border-radius:var(--radius-xl); font-weight:700; }
      select.active-filter { border-color:var(--coral); background-color:#fff5f5; }
    </style>
  </head>
  <body>
    <div class="container">
      <header class="header">
        <div class="header-left">
          <div class="logo-circle">🐾</div>
          <h1 class="site-title">FurEver Home</h1>
        </div>
        <nav class="header-right">
          <a href="index.php"  class="nav-link">Home</a>
          <a href="adopt.php"  class="nav-link active">Adopt</a>
          <a href="about.php"  class="nav-link">About</a>
          <?php if ($loggedIn): ?>
            <span style="font-size:.9rem;font-weight:700;color:var(--brown);padding:8px 14px;">Hi, <?= $firstname ?>! 👋</span>
            <a href="actions/logout.php" class="btn btn-ghost">Logout</a>
          <?php else: ?>
            <a href="login.php"    class="btn btn-ghost">Sign in</a>
            <a href="register.php" class="btn btn-primary">Register</a>
          <?php endif; ?>
        </nav>
        <button class="hamburger" onclick="document.querySelector('.header-right').classList.toggle('open')">☰</button>
      </header>

      <section class="page-hero">
        <h1>Find Your <em>Perfect</em> Pet 🐾</h1>
        <p>Browse our verified listings and meet your new best friend.</p>
      </section>

      <!-- FILTERS — submitted as GET so URL is shareable/bookmarkable -->
      <form method="GET" action="adopt.php">
        <div class="filters-bar">
          <div class="filter-group">
            <label>Pet Type</label>
            <select name="type" <?= $fType !== 'all' ? 'class="active-filter"' : '' ?>>
              <option value="all"    <?= sel('all',    $fType) ?>>All Pets</option>
              <option value="dog"    <?= sel('dog',    $fType) ?>>🐕 Dogs</option>
              <option value="cat"    <?= sel('cat',    $fType) ?>>🐈 Cats</option>
              <option value="rabbit" <?= sel('rabbit', $fType) ?>>🐇 Rabbits</option>
              <option value="bird"   <?= sel('bird',   $fType) ?>>🦜 Birds</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Age</label>
            <select name="age" <?= $fAge !== 'all' ? 'class="active-filter"' : '' ?>>
              <option value="all"    <?= sel('all',    $fAge) ?>>Any Age</option>
              <option value="young"  <?= sel('young',  $fAge) ?>>Young (0–2y)</option>
              <option value="adult"  <?= sel('adult',  $fAge) ?>>Adult (2–5y)</option>
              <option value="senior" <?= sel('senior', $fAge) ?>>Senior (5y+)</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Gender</label>
            <select name="gender" <?= $fGender !== 'all' ? 'class="active-filter"' : '' ?>>
              <option value="all"    <?= sel('all',    $fGender) ?>>Any</option>
              <option value="male"   <?= sel('male',   $fGender) ?>>Male</option>
              <option value="female" <?= sel('female', $fGender) ?>>Female</option>
            </select>
          </div>
          <div class="filter-group">
            <label>City</label>
            <select name="city" <?= $fCity !== 'all' ? 'class="active-filter"' : '' ?>>
              <option value="all"       <?= sel('all',       $fCity) ?>>All Cities</option>
              <option value="Mumbai"    <?= sel('Mumbai',    $fCity) ?>>Mumbai</option>
              <option value="Delhi"     <?= sel('Delhi',     $fCity) ?>>Delhi</option>
              <option value="Bengaluru" <?= sel('Bengaluru', $fCity) ?>>Bengaluru</option>
              <option value="Pune"      <?= sel('Pune',      $fCity) ?>>Pune</option>
              <option value="Chennai"   <?= sel('Chennai',   $fCity) ?>>Chennai</option>
              <option value="Hyderabad" <?= sel('Hyderabad', $fCity) ?>>Hyderabad</option>
              <option value="Kolkata"   <?= sel('Kolkata',   $fCity) ?>>Kolkata</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Search 🔍</button>
          <a href="adopt.php" class="btn btn-ghost">Reset</a>
        </div>
      </form>

      <!-- LISTINGS -->
      <section class="listings-section">
        <div class="listings-header">
          <div style="display:flex;align-items:center;gap:10px;">
            <h2>Pets Available</h2>
            <span id="result-count"><?= $count ?> pet<?= $count !== 1 ? 's' : '' ?></span>
          </div>
          <div class="sort-row">
            <span>Sort by:</span>
            <select onchange="this.form && this.form.submit()" id="sort-select"
                    onchange="window.location='adopt.php?type=<?= $fType ?>&age=<?= $fAge ?>&gender=<?= $fGender ?>&city=<?= $fCity ?>&sort='+this.value">
              <option value="newest"    <?= sel('newest',    $fSort) ?>>Newest First</option>
              <option value="oldest"    <?= sel('oldest',    $fSort) ?>>Oldest First</option>
              <option value="name-asc"  <?= sel('name-asc',  $fSort) ?>>Name A–Z</option>
              <option value="name-desc" <?= sel('name-desc', $fSort) ?>>Name Z–A</option>
            </select>
          </div>
        </div>

        <div class="listings-grid">
          <?php if (empty($pets)): ?>
            <div class="empty-state">
              <span class="empty-emoji">🔍</span>
              <h3>No pets found</h3>
              <p>Try adjusting your filters — there are plenty of pets waiting!</p>
              <a href="adopt.php" class="btn btn-primary" style="margin-top:16px;">Clear Filters</a>
            </div>
          <?php else: ?>
            <?php foreach ($pets as $pet):
              $typeTag   = htmlspecialchars($pet['type']);
              $tagClass  = "tag-{$typeTag}";
              $genderTag = $pet['gender'] === 'male' ? 'tag-male' : 'tag-female';
            ?>
            <div class="listing-card">
              <div class="listing-img">
                <img src="<?= htmlspecialchars($pet['image_path']) ?>"
                     alt="<?= htmlspecialchars($pet['name']) ?>" loading="lazy" />
              </div>
              <div class="listing-body">
                <div class="listing-tags">
                  <span class="tag <?= $tagClass ?>"><?= ucfirst($typeTag) ?></span>
                  <span class="tag <?= $genderTag ?>"><?= ucfirst($pet['gender']) ?></span>
                </div>
                <h3><?= htmlspecialchars($pet['name']) ?></h3>
                <p class="listing-meta"><?= htmlspecialchars($pet['breed']) ?> · <?= ageLabel((float)$pet['age_years']) ?></p>
                <p class="listing-desc"><?= htmlspecialchars($pet['description']) ?></p>
                <div class="listing-footer">
                  <span class="listing-loc">📍 <?= htmlspecialchars($pet['city']) ?></span>
                  <?php if ($loggedIn): ?>
                    <a href="adopt-request.php?pet_id=<?= (int)$pet['id'] ?>" class="btn btn-primary btn-sm">Adopt Me</a>
                  <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">Sign in to Adopt</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <footer class="footer">
        <div class="footer-top">
          <div class="footer-brand">
            <div class="logo-circle">🐾</div>
            <h2>FurEver Home</h2>
            <p>Connecting loving hearts with furry friends since 2024.</p>
          </div>
          <div class="footer-links">
            <h4>Quick Links</h4>
            <a href="index.php">Home</a>
            <a href="adopt.php">Adopt a Pet</a>
            <a href="about.php">About Us</a>
          </div>
          <div class="footer-links">
            <h4>Pets</h4>
            <a href="adopt.php?type=dog">Dogs</a>
            <a href="adopt.php?type=cat">Cats</a>
            <a href="adopt.php?type=rabbit">Rabbits</a>
            <a href="adopt.php?type=bird">Birds</a>
          </div>
          <div class="footer-links">
            <h4>Contact</h4>
            <a href="#">hello@fureverhome.in</a>
          </div>
        </div>
        <div class="footer-bottom"><p>© 2024 FurEver Home. Made with ❤️ for animals everywhere.</p></div>
      </footer>
    </div>

    <script>
      // Sort dropdown — rebuild URL preserving other filters
      document.getElementById('sort-select').addEventListener('change', function() {
        const params = new URLSearchParams(window.location.search);
        params.set('sort', this.value);
        window.location.search = params.toString();
      });
    </script>
  </body>
</html>
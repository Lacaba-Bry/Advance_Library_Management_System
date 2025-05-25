<?php
require_once __DIR__ . '/backend/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// You correctly set these in login.php:
$accountId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email'] ?? 'Unknown';
$userType = $_SESSION['user_type'] ?? 'Free'; // Plan name: Free, Premium, or VIP
$userName = $_SESSION['fullname'] ?? 'User';

// Query to get the user's plan and avatar from the profiles table
$stmt = $conn->prepare("
    SELECT p.Plan_Name, pr.Avatar
    FROM accountlist a
    LEFT JOIN plans p ON a.Plan_ID = p.Plan_ID
    LEFT JOIN profiles pr ON a.Account_ID = pr.Account_ID
    WHERE a.Account_ID = ?
");
$stmt->bind_param("i", $accountId);
$stmt->execute();
$stmt->bind_result($userType, $avatar); // Retrieve userType and avatar
$stmt->fetch();
$stmt->close();

// Update session (optional, to keep in sync)
$_SESSION['user_type'] = $userType ?? 'Free';

// Check if the avatar image is set, otherwise use default
$avatar = $avatar ?: 'image/profile/defaultprofile.jpg'; // Set default image if $avatar is empty
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="./css/home.css">
  <title>Haven Library - User Dashboard</title>
</head>
<body>

<header class="top-nav">
  <div class="left-section">
  <img src="Logo.jpg" class="logo" alt="Logo" />
    <nav class="nav-links">
      <div class="dropdown">
        <button class="dropbtn">
          Browse 
          <span class="material-icons dropdown-icon">arrow_drop_down</span>
        </button>
        <div class="dropdown-content">
          <a href="home.php">Home</a>
          <a href="genres.php">Genres</a>
        </div>
      </div>
    </nav>
  </div>

  <div class="center-section">
    <input type="text" class="search-bar" placeholder="Search" />
  </div>

  <div class="right-section">
    <button class="premium-btn">⚡ Try Premium</button>

    <div class="profile dropdown">
      <div class="user-info">
    <img src="<?php echo htmlspecialchars($avatar); ?>" class="avatar" alt="User" />
      </div>
      <div class="dropdown-content">
        <a href="index/profile.php">Profile</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>

    <span class="user-type"><?php echo htmlspecialchars($userType); ?></span>
  </div>
</header>


  <section class="best-seller-section">
  <div class="best-seller-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveBestSellerCarousel(-1)">&#8249;</button>
    <div class="best-seller-carousel" id="bestSellerCarousel">
      <?php for ($i = 1; $i <= 8; $i++): ?>
        <div class="best-seller-item">
          <img src="Books/BestSeller/book<?php echo $i; ?>.jpg" alt="Best Seller Book <?php echo $i; ?>">
          <div class="best-seller-info">
            <h3>Book Title <?php echo $i; ?></h3>
            <p>Author Name</p>
            <p class="price">$<?php echo rand(10, 30); ?></p>
          </div>
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveBestSellerCarousel(1)">&#8250;</button>
  </div>
</section>


  <section class="trending-section">
  <h2>Trending Now</h2>
  <div class="carousel-container">
    <div class="trending-carousel">
      <div class="trending-item"><span class="rank">1</span><img src="Books/Trending/Possesive.jpg" alt="Possessive"></div>
      <div class="trending-item"><span class="rank">2</span><img src="Books/Trending/Fifty.jpg" alt="Fifty"></div>
      <div class="trending-item"><span class="rank">3</span><img src="Books/Trending/Seduce.jpg" alt="Seduce"></div>
      <div class="trending-item"><span class="rank">4</span><img src="Books/Trending/Stepdad.jpg" alt="Stepdad"></div>
      <div class="trending-item"><span class="rank">5</span><img src="Books/Trending/Dominant.jpg" alt="Dominant"></div>
      <div class="trending-item"><span class="rank">6</span><img src="Books/Trending/after.jpg" alt="Possessive"></div>
      <div class="trending-item"><span class="rank">7</span><img src="Books/Trending/assist.jpg" alt="Fifty"></div>
      <div class="trending-item"><span class="rank">8</span><img src="Books/Trending/crave.jpg" alt="Seduce"></div>
      <div class="trending-item"><span class="rank">9</span><img src="Books/Trending/Desire.jpg" alt="Stepdad"></div>
      <div class="trending-item"><span class="rank">10</span><img src="Books/Trending/obses.jpg" alt="Dominant"></div>
    </div>
  </div>
</section>

<section class="continue-reading-section">
  <h2>Continue Reading</h2>
  <div class="continue-reading-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveContinueCarousel(-1)">&#8249;</button>
    <div class="continue-carousel" id="continueCarousel">
      <?php for ($i = 1; $i <= 6; $i++): ?>
        <?php $progress = rand(10, 80); ?>
        <div class="continue-item">
          <img src="Books/Continue/book<?php echo $i; ?>.jpg" alt="Continue Book <?php echo $i; ?>">
          <div class="progress-bar">
            <div class="progress" style="width: <?php echo $progress; ?>%;"></div>
          </div>
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveContinueCarousel(1)">&#8250;</button>
  </div>
</section>


<section class="free-section">
  <h2>Top Free</h2>
  <div class="free-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveFreeCarousel(-1)">&#8249;</button>
    <div class="free-carousel" id="freeCarousel">
      <!-- Add your free items here -->
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="free-item">
          <img src="Books/Free/book<?php echo $i; ?>.jpg" alt="Free Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveFreeCarousel(1)">&#8250;</button>
  </div>
</section>

<!-- Top Premium Section -->
<section class="premium-section">
  <h2>Top Premium</h2>
  <div class="premium-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="movePremiumCarousel(-1)">&#8249;</button>
    <div class="premium-carousel" id="premiumCarousel">
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="premium-item">
          <img src="Books/Premium/book<?php echo $i; ?>.jpg" alt="Premium Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="movePremiumCarousel(1)">&#8250;</button>
  </div>
</section>



<section class="wattpad-classics-section py-4">
  <h2 class="text-center">Wattpad Classics</h2>
  <div class="carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveWattpadClassicsCarousel(-1)">&#8249;</button>
    <div class="carousel" id="wattpadClassicsCarousel">
      <?php 
      // Define the books to display in the carousel
      $books = [
        ["title" => "I Miss You Too", "image" => "sample1.jpg", "description" => "When Carmen Cruz finds herself in competition with her ex-girlfriend Briar Sutton for the school play...", "progress" => "95.4K", "status" => "Complete"],
         ["title" => "Another Book", "image" => "sample1.jpg", "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sed tortor leo...", "progress" => "150K", "status" => "Complete"],
        ["title" => "Another Book", "image" => "sample1.jpg", "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sed tortor leo...", "progress" => "150K", "status" => "Complete"],
        ["title" => "Book Four", "image" => "sample1.jpg", "description" => "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque...", "progress" => "300K", "status" => "Complete"],
        ["title" => "Book Five", "image" => "Books/Classic/book5.jpg", "description" => "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur...", "progress" => "550K", "status" => "Complete"],
        ["title" => "Book Six", "image" => "Books/Classic/book6.jpg", "description" => "Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua...", "progress" => "400K", "status" => "Complete"]
      ];

      // Iterate through the books array and display each pair of books as a slide
      for ($i = 0; $i < count($books); $i+=2): ?>
        <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
          <div class="row justify-content-center">
            <?php for ($j = $i; $j < $i + 2 && $j < count($books); $j++): ?>
              <div class="col-12 col-md-6 mb-4">
                <div class="card wattpad-classics-card">
                  <img src="<?php echo $books[$j]['image']; ?>" class="card-img-top" alt="<?php echo $books[$j]['title']; ?>">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $books[$j]['title']; ?></h5>
                    <p class="card-text"><?php echo $books[$j]['description']; ?></p>
                    <p class="text-muted"><?php echo $books[$j]['progress']; ?> | <?php echo $books[$j]['status']; ?></p>
                  </div>
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveWattpadClassicsCarousel(1)">&#8250;</button>
  </div>
</section>


<section class="fiction-section">
  <h2>Top in Fiction</h2>
  <div class="fiction-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel(-1)">&#8249;</button>
    <div class="fiction-carousel" id="fictionCarousel">
      <!-- Add your fiction book items here -->
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="fiction-item">
          <img src="Books/Fiction/book<?php echo $i; ?>.jpg" alt="Fiction Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel(1)">&#8250;</button>
  </div>
</section>


<!-- Top Non-Fiction Section -->
<section class="nonfiction-section">
  <h2>Top Non-Fiction</h2>
  <div class="nonfiction-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveNonFictionCarousel(-1)">&#8249;</button>
    <div class="nonfiction-carousel" id="nonfictionCarousel">
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="nonfiction-item">
          <img src="Books/NonFiction/book<?php echo $i; ?>.jpg" alt="Non-Fiction Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveNonFictionCarousel(1)">&#8250;</button>
  </div>
</section>

<!-- Top Science Fiction Section -->
<section class="sciencefiction-section">
  <h2>Top Science Fiction</h2>
  <div class="sciencefiction-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveScienceFictionCarousel(-1)">&#8249;</button>
    <div class="sciencefiction-carousel" id="sciencefictionCarousel">
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="sciencefiction-item">
          <img src="Books/ScienceFiction/book<?php echo $i; ?>.jpg" alt="Science Fiction Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveScienceFictionCarousel(1)">&#8250;</button>
  </div>
</section>

<!-- Top Fantasy Section -->
<section class="fantasy-section">
  <h2>Top Fantasy</h2>
  <div class="fantasy-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveFantasyCarousel(-1)">&#8249;</button>
    <div class="fantasy-carousel" id="fantasyCarousel">
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="fantasy-item">
          <img src="Books/Fantasy/book<?php echo $i; ?>.jpg" alt="Fantasy Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveFantasyCarousel(1)">&#8250;</button>
  </div>
</section>

<!-- Top Mystery Section -->
<section class="mystery-section">
  <h2>Top Mystery</h2>
  <div class="mystery-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveMysteryCarousel(-1)">&#8249;</button>
    <div class="mystery-carousel" id="mysteryCarousel">
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="mystery-item">
          <img src="Books/Mystery/book<?php echo $i; ?>.jpg" alt="Mystery Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveMysteryCarousel(1)">&#8250;</button>
  </div>
</section>

<!-- Top Romance Section -->
<section class="romance-section">
  <h2>Top Romance</h2>
  <div class="romance-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveRomanceCarousel(-1)">&#8249;</button>
    <div class="romance-carousel" id="romanceCarousel">
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="romance-item">
          <img src="Books/Romance/book<?php echo $i; ?>.jpg" alt="Romance Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveRomanceCarousel(1)">&#8250;</button>
  </div>
</section>

<!-- Top Horror Section -->
<section class="horror-section">
  <h2>Top Horror</h2>
  <div class="horror-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveHorrorCarousel(-1)">&#8249;</button>
    <div class="horror-carousel" id="horrorCarousel">
      <?php for ($i = 1; $i <= 25; $i++): ?>
        <div class="horror-item">
          <img src="Books/Horror/book<?php echo $i; ?>.jpg" alt="Horror Book <?php echo $i; ?>">
        </div>
      <?php endfor; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveHorrorCarousel(1)">&#8250;</button>
  </div>
</section>

<footer class="footer">
  <div class="footer-container">
    <!-- Left Section: Haven Library Information -->
    <div class="footer-left">
      <h2>Haven Library</h2>
      <p>Advanced Library Management System</p>
    </div>
    
 <div class="footer-right">
      <h3>Follow Us</h3>
      <div class="social-media">
        <a href="#" class="social-icon">Facebook</a>
        <a href="#" class="social-icon">Twitter</a>
        <a href="#" class="social-icon">LinkedIn</a>
        <a href="#" class="social-icon">Instagram</a>
      </div>
      <p>&copy; 2025 Haven Library. All Rights Reserved.</p>
    </div>

    <!-- Center Section: Quick Links -->
    <div class="footer-center">
      <div class="footer-links">
        <div class="footer-links-group">
          <h3>About</h3>
          <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Our Services</a></li>
            <li><a href="#">Careers</a></li>
          </ul>
        </div>
      </div>
    </div>

   
  </div>
</footer>


















<script>
let currentSlide = 0;
const itemsPerPage = 10;
const totalItems = 25;
const carousel = document.getElementById("fictionCarousel");

function moveCarousel(direction) {
  const maxSlide = Math.ceil(totalItems / itemsPerPage) - 1;
  currentSlide += direction;

  if (currentSlide < 0) currentSlide = 0;
  if (currentSlide > maxSlide) currentSlide = maxSlide;

  const scrollAmount = currentSlide * (160 + 16) * itemsPerPage;
  carousel.scrollTo({ left: scrollAmount, behavior: "smooth" });
}

let currentBestSellerSlide = 0;
const bestSellerItemsPerPage = 2;
const totalBestSellerItems = 8;
const bestSellerCarousel = document.getElementById("bestSellerCarousel");

function moveBestSellerCarousel(direction) {
  const maxSlide = Math.ceil(totalBestSellerItems / bestSellerItemsPerPage) - 1;
  currentBestSellerSlide += direction;

  if (currentBestSellerSlide < 0) currentBestSellerSlide = 0;
  if (currentBestSellerSlide > maxSlide) currentBestSellerSlide = maxSlide;

  const scrollAmount = currentBestSellerSlide * (800 + 20) * bestSellerItemsPerPage;
  bestSellerCarousel.scrollTo({ left: scrollAmount, behavior: "smooth" });
}
let currentContinueSlide = 0;
const continueItemsPerPage = 3;
const totalContinueItems = 6;
const continueCarousel = document.getElementById("continueCarousel");

function moveContinueCarousel(direction) {
  const maxSlide = Math.ceil(totalContinueItems / continueItemsPerPage) - 1;
  currentContinueSlide += direction;

  if (currentContinueSlide < 0) currentContinueSlide = 0;
  if (currentContinueSlide > maxSlide) currentContinueSlide = maxSlide;

  const scrollAmount = currentContinueSlide * (300 + 20) * continueItemsPerPage;
  continueCarousel.scrollTo({ left: scrollAmount, behavior: "smooth" });}



let currentsSlide = 0;
const wattpadItemsPerPage = 2; // Two books per slide
const totalWattpadItems = 6;  // Number of books
const wattpadCarousel = document.getElementById("wattpadClassicsCarousel");

function moveWattpadClassicsCarousel(direction) {
  const maxSlide = Math.ceil(totalWattpadItems / wattpadItemsPerPage) - 1;
  currentsSlide += direction;

  if (currentsSlide < 0) currentsSlide = 0;
  if (currentsSlide > maxSlide) currentsSlide = maxSlide;

  const scrollAmount = currentsSlide * (wattpadCarousel.offsetWidth); // Calculate full carousel width
  wattpadCarousel.scrollTo({ left: scrollAmount, behavior: "smooth" });
}


let currentSlideFree = 0;
const itemsPerPageFree = 10;
const totalItemsFree = 25;
const carouselFree = document.getElementById("freeCarousel");

function moveFreeCarousel(direction) {
  const maxSlideFree = Math.ceil(totalItemsFree / itemsPerPageFree) - 1;
  currentSlideFree += direction;

  if (currentSlideFree < 0) currentSlideFree = 0;
  if (currentSlideFree > maxSlideFree) currentSlideFree = maxSlideFree;

  const scrollAmountFree = currentSlideFree * (160 + 16) * itemsPerPageFree;
  carouselFree.scrollTo({ left: scrollAmountFree, behavior: "smooth" });
}

// Top Premium Carousel
let currentSlidePremium = 0;
const itemsPerPagePremium = 10;
const totalItemsPremium = 25;
const carouselPremium = document.getElementById("premiumCarousel");

function movePremiumCarousel(direction) {
  const maxSlidePremium = Math.ceil(totalItemsPremium / itemsPerPagePremium) - 1;
  currentSlidePremium += direction;

  if (currentSlidePremium < 0) currentSlidePremium = 0;
  if (currentSlidePremium > maxSlidePremium) currentSlidePremium = maxSlidePremium;

  const scrollAmountPremium = currentSlidePremium * (160 + 16) * itemsPerPagePremium;
  carouselPremium.scrollTo({ left: scrollAmountPremium, behavior: "smooth" });
}


// Top Non-Fiction Carousel
let currentSlideNonFiction = 0;
const itemsPerPageNonFiction = 10;
const totalItemsNonFiction = 25;
const carouselNonFiction = document.getElementById("nonfictionCarousel");

function moveNonFictionCarousel(direction) {
  const maxSlideNonFiction = Math.ceil(totalItemsNonFiction / itemsPerPageNonFiction) - 1;
  currentSlideNonFiction += direction;

  if (currentSlideNonFiction < 0) currentSlideNonFiction = 0;
  if (currentSlideNonFiction > maxSlideNonFiction) currentSlideNonFiction = maxSlideNonFiction;

  const scrollAmountNonFiction = currentSlideNonFiction * (160 + 16) * itemsPerPageNonFiction;
  carouselNonFiction.scrollTo({ left: scrollAmountNonFiction, behavior: "smooth" });
}

// Top Science Fiction Carousel
let currentSlideScienceFiction = 0;
const itemsPerPageScienceFiction = 10;
const totalItemsScienceFiction = 25;
const carouselScienceFiction = document.getElementById("sciencefictionCarousel");

function moveScienceFictionCarousel(direction) {
  const maxSlideScienceFiction = Math.ceil(totalItemsScienceFiction / itemsPerPageScienceFiction) - 1;
  currentSlideScienceFiction += direction;

  if (currentSlideScienceFiction < 0) currentSlideScienceFiction = 0;
  if (currentSlideScienceFiction > maxSlideScienceFiction) currentSlideScienceFiction = maxSlideScienceFiction;

  const scrollAmountScienceFiction = currentSlideScienceFiction * (160 + 16) * itemsPerPageScienceFiction;
  carouselScienceFiction.scrollTo({ left: scrollAmountScienceFiction, behavior: "smooth" });
}

// Top Fantasy Carousel
let currentSlideFantasy = 0;
const itemsPerPageFantasy = 10;
const totalItemsFantasy = 25;
const carouselFantasy = document.getElementById("fantasyCarousel");

function moveFantasyCarousel(direction) {
  const maxSlideFantasy = Math.ceil(totalItemsFantasy / itemsPerPageFantasy) - 1;
  currentSlideFantasy += direction;

  if (currentSlideFantasy < 0) currentSlideFantasy = 0;
  if (currentSlideFantasy > maxSlideFantasy) currentSlideFantasy = maxSlideFantasy;

  const scrollAmountFantasy = currentSlideFantasy * (160 + 16) * itemsPerPageFantasy;
  carouselFantasy.scrollTo({ left: scrollAmountFantasy, behavior: "smooth" });
}

// Top Mystery Carousel
let currentSlideMystery = 0;
const itemsPerPageMystery = 10;
const totalItemsMystery = 25;
const carouselMystery = document.getElementById("mysteryCarousel");

function moveMysteryCarousel(direction) {
  const maxSlideMystery = Math.ceil(totalItemsMystery / itemsPerPageMystery) - 1;
  currentSlideMystery += direction;

  if (currentSlideMystery < 0) currentSlideMystery = 0;
  if (currentSlideMystery > maxSlideMystery) currentSlideMystery = maxSlideMystery;

  const scrollAmountMystery = currentSlideMystery * (160 + 16) * itemsPerPageMystery;
  carouselMystery.scrollTo({ left: scrollAmountMystery, behavior: "smooth" });
}

// Top Romance Carousel
let currentSlideRomance = 0;
const itemsPerPageRomance = 10;
const totalItemsRomance = 25;
const carouselRomance = document.getElementById("romanceCarousel");

function moveRomanceCarousel(direction) {
  const maxSlideRomance = Math.ceil(totalItemsRomance / itemsPerPageRomance) - 1;
  currentSlideRomance += direction;

  if (currentSlideRomance < 0) currentSlideRomance = 0;
  if (currentSlideRomance > maxSlideRomance) currentSlideRomance = maxSlideRomance;

  const scrollAmountRomance = currentSlideRomance * (160 + 16) * itemsPerPageRomance;
  carouselRomance.scrollTo({ left: scrollAmountRomance, behavior: "smooth" });
}

// Top Horror Carousel
let currentSlideHorror = 0;
const itemsPerPageHorror = 10;
const totalItemsHorror = 25;
const carouselHorror = document.getElementById("horrorCarousel");

function moveHorrorCarousel(direction) {
  const maxSlideHorror = Math.ceil(totalItemsHorror / itemsPerPageHorror) - 1;
  currentSlideHorror += direction;

  if (currentSlideHorror < 0) currentSlideHorror = 0;
  if (currentSlideHorror > maxSlideHorror) currentSlideHorror = maxSlideHorror;

  const scrollAmountHorror = currentSlideHorror * (160 + 16) * itemsPerPageHorror;
  carouselHorror.scrollTo({ left: scrollAmountHorror, behavior: "smooth" });
}
</script>



</body>
</html>

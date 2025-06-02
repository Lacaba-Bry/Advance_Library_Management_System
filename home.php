<?php
require_once __DIR__ . '/backend/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// --------------------------------------------------------------------------
// USER SESSION AND PROFILE DATA
// --------------------------------------------------------------------------

$accountId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email'] ?? 'Unknown';
$userType = $_SESSION['user_type'] ?? 'Free';
$userName = $_SESSION['fullname'] ?? 'User';

// Get Plan Name and Avatar
$stmt = $conn->prepare("
    SELECT p.Plan_Name, pr.Avatar
    FROM accountlist a
    LEFT JOIN plans p ON a.Plan_ID = p.Plan_ID
    LEFT JOIN profiles pr ON a.Account_ID = pr.Account_ID
    WHERE a.Account_ID = ?
");

if ($stmt === false) {
    error_log("Prepare failed: " . $conn->error);
    die("Database error: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $accountId);
$stmt->execute();

if ($stmt->errno) {
    error_log("Execute failed: " . $stmt->error);
    die("Database query failed: " . htmlspecialchars($stmt->error));
}

$stmt->bind_result($userType, $avatar);
$stmt->fetch();
$stmt->close();

$_SESSION['user_type'] = $userType ?? 'Free';
$avatar = $avatar ?: 'image/profile/defaultprofile.jpg';

// --------------------------------------------------------------------------
// BOOK FETCH FUNCTIONS
// --------------------------------------------------------------------------

// Fetch books by plan type
function getBooksByPlanType($conn, $planType) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type FROM books WHERE Plan_type = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $planType);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    return $books;
}

// Fetch all books
function getBooks($conn) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price FROM books";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}

function getBestSellersPaid($conn, $limit = 10) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type 
            FROM books 
            WHERE Plan_type = 'Paid' 
            ORDER BY Price DESC 
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    return $books;
}


// --------------------------------------------------------------------------
// FETCH BOOK DATA FOR EACH SECTION
// --------------------------------------------------------------------------

$freeBooks = getBooksByPlanType($conn, 'Free');
$premiumBooks = getBooksByPlanType($conn, 'Premium');
$paidBooks = getBooksByPlanType($conn, 'Paid');
$bestSellerPaidBooks = getBestSellersPaid($conn);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="css/index/home.css">
  <script src="javascript/home.js"></script>
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
        <a href="backend/logout.php">Logout</a>
      </div>
    </div>

    <span class="user-type"><?php echo htmlspecialchars($userType); ?></span>
</header>


<!-- Best Seller Section -->
<section class="best-seller-section">
  <h2>Best Sellers</h2>
  <div class="best-seller-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveBestSellerCarousel(-1)">&#8249;</button>
    <div class="best-seller-carousel" id="bestSellerCarousel">
      <?php
        // Fetch best seller books from Paid plan_type ordered by Views DESC
        $bestSellerBooks = getBestSellersPaid($conn);  // Function already defined in PHP block
        foreach ($bestSellerBooks as $book): 
      ?>
        <div class="best-seller-item">
          <img src="<?= htmlspecialchars($book['Book_Cover']) ?>" alt="<?= htmlspecialchars($book['Title']) ?>">
          <div class="best-seller-info">
            <h3><?= htmlspecialchars($book['Title']) ?></h3>
            <p><?= htmlspecialchars($book['Author']) ?></p>
            <p class="price">₱<?= htmlspecialchars($book['Price']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveBestSellerCarousel(1)">&#8250;</button>
  </div>
</section>


<section class="trending-section">
  <h2>Trending Now</h2>
</section>

<section class="continue-reading-section">
  <h2>Continue Reading</h2>
</section>

<!-- Top Free Section -->
<section class="free-section">
  <h2>Top Free</h2>
  <div class="free-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveFreeCarousel(-1)">&#8249;</button>
    <div class="free-carousel" id="freeCarousel">
      <?php foreach ($freeBooks as $book): ?>
        <div class="free-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php endforeach; ?>
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
      <?php foreach ($premiumBooks as $book): ?>
        <div class="premium-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php endforeach; ?>
    </div>
    <button class="carousel-btn next-btn" onclick="movePremiumCarousel(1)">&#8250;</button>
  </div>
</section>



<section class="wattpad-classics-section py-4">
  <h2 class="text-center">Wattpad Classics (Top Genres: <?php echo htmlspecialchars(implode(", ", $topGenres)); ?>)</h2>  <!-- Dynamic top genres -->
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
      ?>
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
       <?php
        $fictionCount = 0;
        foreach ($books as $book):
            if ($fictionCount >= 25) break;  // Limit to 25 fiction books

            // Basic placeholder logic for genre.  REPLACE WITH ACTUAL LOGIC
            if ($book['Genre'] == "Fiction"):
              $fictionCount++;
        ?>
        <div class="fiction-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
        <?php
            endif;  // if genre fiction
          endforeach; // foreach books
         ?>
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
        <?php
        $nonFictionCount = 0;
        foreach ($books as $book):
            if ($nonFictionCount >= 25) break;  // Limit to 25 non fiction books

            // Basic placeholder logic for genre.  REPLACE WITH ACTUAL LOGIC
            if ($book['Genre'] == "Non-Fiction"):
              $nonFictionCount++;
        ?>
        <div class="nonfiction-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
          <?php
            endif;  // if genre non fiction
          endforeach; // foreach books
         ?>
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
          <?php
        $sciFiCount = 0;
        foreach ($books as $book):
            if ($sciFiCount >= 25) break;  // Limit to 25 science fiction books

            // Basic placeholder logic for genre.  REPLACE WITH ACTUAL LOGIC
            if ($book['Genre'] == "Science Fiction"):
              $sciFiCount++;
        ?>
        <div class="sciencefiction-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
              <?php
            endif;  // if genre science fiction
          endforeach; // foreach books
         ?>
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
          <?php
        $fantasyCount = 0;
        foreach ($books as $book):
            if ($fantasyCount >= 25) break;  // Limit to 25 fantasy books

            // Basic placeholder logic for genre.  REPLACE WITH ACTUAL LOGIC
            if ($book['Genre'] == "Fantasy"):
              $fantasyCount++;
        ?>
        <div class="fantasy-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
              <?php
            endif;  // if genre fantasy
          endforeach; // foreach books
         ?>
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
          <?php
        $mysteryCount = 0;
        foreach ($books as $book):
            if ($mysteryCount >= 25) break;  // Limit to 25 mystery books

            // Basic placeholder logic for genre.  REPLACE WITH ACTUAL LOGIC
            if ($book['Genre'] == "Mystery"):
              $mysteryCount++;
        ?>
        <div class="mystery-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
              <?php
            endif;  // if genre mystery
          endforeach; // foreach books
         ?>
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
          <?php
        $romanceCount = 0;
        foreach ($books as $book):
            if ($romanceCount >= 25) break;  // Limit to 25 romance books

            // Basic placeholder logic for genre.  REPLACE WITH ACTUAL LOGIC
            if ($book['Genre'] == "Romance"):
              $romanceCount++;
        ?>
        <div class="romance-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
              <?php
            endif;  // if genre romance
          endforeach; // foreach books
         ?>
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
          <?php
        $horrorCount = 0;
        foreach ($books as $book):
            if ($horrorCount >= 25) break;  // Limit to 25 horror books

            // Basic placeholder logic for genre.  REPLACE WITH ACTUAL LOGIC
            if ($book['Genre'] == "Horror"):
              $horrorCount++;
        ?>
        <div class="horror-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
              <?php
            endif;  // if genre horror
          endforeach; // foreach books
         ?>
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





</body>
</html>
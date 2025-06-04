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
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, ISBN
            FROM books
            WHERE Plan_type = 'Paid'
            ORDER BY Price DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        return $books; // Or handle the error in a more appropriate way
    }
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    return $books;
}

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
  <link rel="stylesheet" href="css/index/homeX.css">
  <script src="javascript/home.js"></script>
  <title>Haven Library - User Dashboard</title>
  
</head>
<body>


<?php include('reusable/header.php'); ?>


<section class="best-seller-section">
  <h2>Best Sellers</h2>
  <div class="best-seller-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveBestSellerCarousel(-1)">&#8249;</button>
    <div class="best-seller-carousel" id="bestSellerCarousel">
      <?php
        // Fetch best seller books from Paid plan_type ordered by Views DESC
        $bestSellerBooks = getBestSellersPaid($conn);  // Function already defined in PHP block
        foreach ($bestSellerBooks as $book):
          // Check if ISBN exists in the $book array before using it
          $previewPath = "Book/" . ucfirst(strtolower($book['Plan_type'])) . "/Preview/";
          if (isset($book['ISBN'])) {
              $previewPath .= $book['ISBN'] . ".php";
          } else {
              // Handle the case where ISBN is missing
              $previewPath .= "default.php"; // Or some other default page
              error_log("ISBN is missing for book ID: " . $book['Book_ID']);
          }
      ?>
        <div class="best-seller-item">
          <!-- Wrap the book cover with a link to the preview page -->
          <a href="<?= htmlspecialchars($previewPath) ?>" class="cover-link">
            <img src="<?= htmlspecialchars($book['Book_Cover']) ?>" alt="<?= htmlspecialchars($book['Title']) ?>">
          </a>
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
      <?php
        // Query to fetch Free books
        $query = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, ISBN FROM books WHERE Plan_type = 'Free' LIMIT 25";
        $result = $conn->query($query);

        // Debugging: Log the query and result
        error_log("Free Section - SQL Query: " . $query);
        if ($result) {
            error_log("Free Section - Number of rows: " . $result->num_rows);
        } else {
            error_log("Free Section - Query failed: " . $conn->error);
        }


        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):

              // Construct the preview path
              $previewPath = "Book/" . ucfirst(strtolower($book['Plan_type'])) . "/Preview/";
              if (isset($book['ISBN'])) {
                  $previewPath .= $book['ISBN'] . ".php";
              } else {
                  // Handle the case where ISBN is missing
                  $previewPath .= "default.php"; // Or some other default page
                  error_log("ISBN is missing for book ID: " . $book['Book_ID']);
              }

              // Debugging: Log book data and preview path
              error_log("Free Section - Book ID: " . $book['Book_ID'] . ", Title: " . $book['Title'] . ", ISBN: " . $book['ISBN'] . ", Plan_type: " . $book['Plan_type']);
              error_log("Free Section - Preview Path: " . $previewPath);

              // Use the full image path directly from the database
              $imagePath = htmlspecialchars($book['Book_Cover']);

              // Debugging: Log the image path
              error_log("Free Section - Image Path: " . $imagePath);

               ?>
            <div class="free-item">
                <a href="<?= htmlspecialchars($previewPath) ?>">
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
                </a>
            </div>
        <?php
            endwhile;
        } else {
            echo "<p>No free books found.</p>";
        }
      ?>
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
            <?php
        // Query to fetch Premium books
        $query = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, ISBN FROM books WHERE Plan_type = 'Premium' LIMIT 25";
        $result = $conn->query($query);

        // Debugging: Log the query and result
        error_log("Premium Section - SQL Query: " . $query);
        if ($result) {
            error_log("Premium Section - Number of rows: " . $result->num_rows);
        } else {
            error_log("Premium Section - Query failed: " . $conn->error);
        }


        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
              // Construct the preview path
              $previewPath = "Book/" . ucfirst(strtolower($book['Plan_type'])) . "/Preview/";
              if (isset($book['ISBN'])) {
                  $previewPath .= $book['ISBN'] . ".php";
              } else {
                  // Handle the case where ISBN is missing
                  $previewPath .= "default.php"; // Or some other default page
                  error_log("ISBN is missing for book ID: " . $book['Book_ID']);
              }

              // Debugging: Log book data and preview path
              error_log("Premium Section - Book ID: " . $book['Book_ID'] . ", Title: " . $book['Title'] . ", ISBN: " . $book['ISBN'] . ", Plan_type: " . $book['Plan_type']);
              error_log("Premium Section - Preview Path: " . $previewPath);

              // Use the full image path directly from the database
              $imagePath = htmlspecialchars($book['Book_Cover']);

              // Debugging: Log the image path
              error_log("Premium Section - Image Path: " . $imagePath);

               ?>
            <div class="premium-item">
                <a href="<?= htmlspecialchars($previewPath) ?>">
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
                </a>
            </div>
        <?php
            endwhile;
        } else {
            echo "<p>No premium books found.</p>";
        }
      ?>
        </div>
        <button class="carousel-btn next-btn" onclick="movePremiumCarousel(1)">&#8250;</button>
    </div>
</section>


<section class="fiction-section">
  <h2>Top in Fiction</h2>
  <div class="fiction-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel('fiction', -1)">&#8249;</button>
    <div class="fiction-carousel" id="fictionCarousel">
      <?php
        // Example query to fetch books of genre 'Fiction'
        $query = "SELECT * FROM books WHERE Genre = 'Fiction' LIMIT 25";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
              // Construct the preview path, same as Best Seller
              $previewPath = "Book/" . ucfirst(strtolower($book['Plan_type'])) . "/Preview/";
              if (isset($book['ISBN'])) {
                  $previewPath .= $book['ISBN'] . ".php";
              } else {
                  // Handle the case where ISBN is missing
                  $previewPath .= "default.php"; // Or some other default page
                  error_log("ISBN is missing for book ID: " . $book['Book_ID']);
              }
      ?>
      <div class="fiction-item">
        <!-- Wrap the book cover with a link to the preview page -->
        <a href="<?= htmlspecialchars($previewPath) ?>" class="cover-link">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </a>
        <div class="fiction-info">
          <h3><?php echo htmlspecialchars($book['Title']); ?></h3>
          <p>By <?php echo htmlspecialchars($book['Author']); ?></p>
          <!-- Add other details if you like -->
        </div>
      </div>
      <?php
            endwhile;
        } else {
            echo "<p>No fiction books found.</p>";
        }
      ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel('fiction', 1)">&#8250;</button>
  </div>
</section>






<!-- Top Non-Fiction Section -->
<section class="nonfiction-section">
  <h2>Top Non-Fiction</h2>
  <div class="nonfiction-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel('nonfiction', -1)">&#8249;</button>
    <div class="nonfiction-carousel" id="nonfictionCarousel">
      <?php
        // Fetch books of genre 'Non-Fiction' from the database
        $query = "SELECT * FROM books WHERE Genre = 'Non-Fiction' LIMIT 25";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
      ?>
        <div class="nonfiction-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php
            endwhile;
        } else {
            echo "<p>No Non-Fiction books found.</p>";
        }
      ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel('nonfiction', 1)">&#8250;</button>
  </div>
</section>

<!-- Top Science Fiction Section -->
<section class="sciencefiction-section">
  <h2>Top Science Fiction</h2>
  <div class="sciencefiction-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel('sciencefiction', -1)">&#8249;</button>
    <div class="sciencefiction-carousel" id="sciencefictionCarousel">
      <?php
        // Fetch books of genre 'Science Fiction' from the database
        $query = "SELECT * FROM books WHERE Genre = 'Science Fiction' LIMIT 25";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
      ?>
        <div class="sciencefiction-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php
            endwhile;
        } else {
            echo "<p>No Science Fiction books found.</p>";
        }
      ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel('sciencefiction', 1)">&#8250;</button>
  </div>
</section>

<!-- Top Fantasy Section -->
<section class="fantasy-section">
  <h2>Top Fantasy</h2>
  <div class="fantasy-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel('fantasy', -1)">&#8249;</button>
    <div class="fantasy-carousel" id="fantasyCarousel">
      <?php
        // Fetch books of genre 'Fantasy' from the database
        $query = "SELECT * FROM books WHERE Genre = 'Fantasy' LIMIT 25";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
      ?>
        <div class="fantasy-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php
            endwhile;
        } else {
            echo "<p>No Fantasy books found.</p>";
        }
      ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel('fantasy', 1)">&#8250;</button>
  </div>
</section>

<!-- Top Mystery Section -->
<section class="mystery-section">
  <h2>Top Mystery</h2>
  <div class="mystery-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel('mystery', -1)">&#8249;</button>
    <div class="mystery-carousel" id="mysteryCarousel">
      <?php
        // Fetch books of genre 'Mystery' from the database
        $query = "SELECT * FROM books WHERE Genre = 'Mystery' LIMIT 25";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
      ?>
        <div class="mystery-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php
            endwhile;
        } else {
            echo "<p>No Mystery books found.</p>";
        }
      ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel('mystery', 1)">&#8250;</button>
  </div>
</section>

<!-- Top Romance Section -->
<section class="romance-section">
  <h2>Top Romance</h2>
  <div class="romance-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel('romance', -1)">&#8249;</button>
    <div class="romance-carousel" id="romanceCarousel">
      <?php
        // Fetch books of genre 'Romance' from the database
        $query = "SELECT * FROM books WHERE Genre = 'Romance' LIMIT 25";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
      ?>
        <div class="romance-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php
            endwhile;
        } else {
            echo "<p>No Romance books found.</p>";
        }
      ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel('romance', 1)">&#8250;</button>
  </div>
</section>

<!-- Top Horror Section -->
<section class="horror-section">
  <h2>Top Horror</h2>
  <div class="horror-carousel-wrapper">
    <button class="carousel-btn prev-btn" onclick="moveCarousel('horror', -1)">&#8249;</button>
    <div class="horror-carousel" id="horrorCarousel">
      <?php
        // Fetch books of genre 'Horror' from the database
        $query = "SELECT * FROM books WHERE Genre = 'Horror' LIMIT 25";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($book = $result->fetch_assoc()):
      ?>
        <div class="horror-item">
          <img src="<?php echo htmlspecialchars($book['Book_Cover']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>">
        </div>
      <?php
            endwhile;
        } else {
            echo "<p>No Horror books found.</p>";
        }
      ?>
    </div>
    <button class="carousel-btn next-btn" onclick="moveCarousel('horror', 1)">&#8250;</button>
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
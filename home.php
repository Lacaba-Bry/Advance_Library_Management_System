<?php
require_once __DIR__ . '/backend/config/config.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;

// --------------------------------------------------------------------------
// USER SESSION AND PROFILE DATA
// --------------------------------------------------------------------------

$accountId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email'] ?? 'Unknown';
$userType = $_SESSION['user_type'] ?? 'Free';
$userName = $_SESSION['fullname'] ?? 'User';

// Get Plan Name, Avatar, and Expiry Date
$stmt = $conn->prepare("
    SELECT p.Plan_Name, pr.Avatar, up.Expiration_Date
    FROM accountlist a
    LEFT JOIN user_plans up ON a.Account_ID = up.Account_ID
    LEFT JOIN plans p ON up.User_Plan_ID = p.Plan_ID
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

$stmt->bind_result($userType, $avatar, $expirationDate);
$stmt->fetch();
$stmt->close();

$_SESSION['user_type'] = $userType ?? 'Free'; // Store plan in the session
$avatar = $avatar ?: 'image/profile/defaultprofile.jpg';

// Check if the Premium plan has expired
$isPremiumExpired = false;

if (!empty($userType) && strcasecmp($userType, 'Premium') === 0) {
    if ($expirationDate !== NULL) {
        $currentDate = new DateTime(); // Current date
        $expirationDateTime = new DateTime($expirationDate); // Expiry date from DB

        if ($expirationDateTime < $currentDate) {
            $isPremiumExpired = true;
            error_log("Premium plan expired for user: " . $accountId);
        }
    } else {
        $isPremiumExpired = false; // No expiration date, active plan
    }
} else {
    $isPremiumExpired = false; // Handle case for other types of plans or null user type
}

// --------------------------------------------------------------------------
// BOOK FETCH FUNCTIONS
// --------------------------------------------------------------------------

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

// Fetch books by plan type
$freeBooks = getBooksByPlanType($conn, 'Free');
$premiumBooks = getBooksByPlanType($conn, 'Premium');
$paidBooks = getBooksByPlanType($conn, 'Paid');
$bestSellerPaidBooks = getBestSellersPaid($conn);

// --------------------------------------------------------------------------
// DISPLAY BOOKS BASED ON USER PLAN
// --------------------------------------------------------------------------

$userPlanType = $_SESSION['user_type'] ?? 'free';  // Default to 'Free' if not set

// Function to check if the user has access to the book
function canAccessBook($userPlanType, $bookPlanType) {
    // VIP and Premium users can access all books
    if ($userPlanType === 'VIP' || $userPlanType === 'Premium') {
        return true;
    }

    // Free users can access only Free and Paid books
    if ($userPlanType === 'Free' && in_array($bookPlanType, ['Free', 'Paid'])) {
        return true;
    }

    return false; // Deny access if none of the conditions match
}

// Get books based on the user's plan type
function getBooksByUserPlan($conn, $userPlanType) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type FROM books WHERE Plan_type IN ";

    if ($userPlanType === 'VIP') {
        $sql .= "('Free', 'Paid', 'Premium', 'VIP')";
    } elseif ($userPlanType === 'Premium') {
        $sql .= "('Free', 'Paid', 'Premium')";
    } else {
        $sql .= "('Free', 'Paid')";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}

// Fetch books based on the user's plan type
$accessibleBooks = getBooksByUserPlan($conn, $userPlanType);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
 
  <link rel="stylesheet" href="css/index/homexx.css">
  <script src="javascript/home.js"></script>
  <title>Haven Library - User Dashboard</title>
</head>
<body>


<?php include('reusable/header.php'); ?>

<!-- Modal for Expired Premium Plan -->
<div id="premiumExpiredModal" class="modal">
    <div class="modal-content">
        <h3>Your Premium Plan Has Expired</h3>
        <p>Expiration Date: <span id="expirationDate"></span></p>
        <p>Your Premium plan has expired. Please renew your plan to continue enjoying the benefits.</p>

        <!-- Renew Button (Triggers Payment Form) -->
        <button id="renewBtn" class="btn btn-primary">Renew Plan</button>

        <!-- Close Button (Cancel Subscription) -->
        <button id="closeBtn" class="btn btn-secondary">Close</button>
    </div>
</div>
<!-- Modal for Payment -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <h3>Enter Payment Details</h3>
        <form id="paymentForm" action="processPayment.php" method="POST">
            <label for="cardNumber">Card Number</label>
            <input type="text" id="cardNumber" name="cardNumber" placeholder="Enter card number" required />

            <label for="expiryDate">Expiry Date</label>
            <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" required />

            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" placeholder="Enter CVV" required />

            <button type="submit" class="btn btn-success">Submit Payment</button>
        </form>

        <!-- Cancel button to go back to the expired plan modal -->
        <button id="paymentCancelBtn" class="btn btn-secondary">Cancel</button>
    </div>
</div>



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
<?php
// Fetch the book details along with the review count, Plan_type, and ISBN
$query = "
    SELECT 
        b.Book_ID, 
        b.Title, 
        b.Author, 
        b.Book_Cover, 
        b.Genre, 
        b.Price, 
        COUNT(r.Review_ID) AS ReviewCount, 
        b.Plan_type, 
        b.ISBN
    FROM books b
    LEFT JOIN reviews r ON b.Book_ID = r.Book_ID
    GROUP BY b.Book_ID
    ORDER BY ReviewCount DESC 
    LIMIT 10
";

$result = $conn->query($query);
?>

<section class="trending-section">
    <h2>Trending Now</h2>
    <div class="trending-items-container">
        <?php 
        // Fetch trending books
        $query = "
            SELECT b.Book_ID,b.Title, b.Author, b.Book_Cover, 
                b.Genre, 
                b.Price, 
                COUNT(r.Review_ID) AS ReviewCount, 
                b.Plan_type, 
                b.ISBN
            FROM books b
            LEFT JOIN reviews r ON b.Book_ID = r.Book_ID
            GROUP BY b.Book_ID
            ORDER BY ReviewCount DESC
            LIMIT 10
        ";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $rank = 1;
            while ($book = $result->fetch_assoc()) {
                $previewPath = "Book/" . ucfirst(strtolower($book['Plan_type'])) . "/Preview/" . $book['ISBN'] . ".php";
                $hasAccess = canAccessBook($userPlanType, $book['Plan_type']);
        ?>
                <div class="trending-item">
                    <div class="rank"><?= $rank ?></div>
                    <a href="<?= htmlspecialchars($previewPath) ?>">
                        <div class="book-cover-container">
                            <img src="<?= htmlspecialchars($book['Book_Cover']) ?>" alt="<?= htmlspecialchars($book['Title']) ?>">
                            <?php if (!$hasAccess): ?>
                                <div class="lock-overlay">
                                    <i class="fas fa-lock"></i> <!-- Lock icon -->
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="trending-info">
                        <span class="plan-type"><?= htmlspecialchars($book['Plan_type']) ?></span>
                        <span class="reviews"><?= $book['ReviewCount'] ?> reviews</span>
                    </div>
                </div>
        <?php 
                $rank++; 
            }
        } else {
            echo "<p>No trending books found.</p>";
        }
        ?>
    </div>
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

                    // Check if the user has access to the premium book
                    $userPlanType = $_SESSION['user_type'] ?? 'Free'; // Get user plan type from session
                    $hasAccess = canAccessBook($userPlanType, $book['Plan_type']);
                    error_log("Premium Section - Book: " . $book['Title'] . ", Plan Type: " . $book['Plan_type'] . ", Has Access: " . ($hasAccess ? 'Yes' : 'No'));
                    error_log("Premium Section - Preview Path: " . $previewPath);

                    // Use the full image path directly from the database
                    $imagePath = htmlspecialchars($book['Book_Cover']);

                    // Debugging: Log the image path
                    error_log("Premium Section - Image Path: " . $imagePath);
            ?>
                    <div class="premium-item">
                        <!-- Book cover with lock icon for restricted books -->
                        <div class="book-cover-container <?php if (!$hasAccess) echo 'disabled'; ?>">
                            <a href="<?= $hasAccess ? htmlspecialchars($previewPath) : '#' ?>">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>" class="book-cover">
                                <?php if (!$hasAccess): ?>
                                    <div class="lock-overlay">
                                        <i class="fas fa-lock"></i> <!-- Lock icon -->
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <!-- Book info -->
                        <div class="premium-info">
                            <h3><?= htmlspecialchars($book['Title']) ?></h3>
                            <p><?= htmlspecialchars($book['Author']) ?></p>
                            <p class="price">₱<?= htmlspecialchars($book['Price']) ?></p>
                        </div>
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

<!-- Footer Section -->
<footer class="footer">
  <div class="footer-links">
    <a href="#">About Us</a>
    <a href="#">Privacy Policy</a>
    <a href="#">Terms of Service</a>
    <a href="#">Contact</a>
  </div>
  <div class="footer-social">
    <a href="#" class="social-icon">Facebook</a>
    <a href="#" class="social-icon">Instagram</a>
    <a href="#" class="social-icon">Twitter</a>
  </div>
  <p class="footer-text">&copy; 2025 Haven Library. All rights reserved.</p>
</footer>


<script>


// Show the modal when the Premium plan has expired
window.addEventListener("load", function() {
    // If the Premium plan is expired, show the renewal modal
    <?php if ($isPremiumExpired): ?>
        document.getElementById("premiumExpiredModal").style.display = "flex";
        // Set expiration date in the modal (you can also use PHP for this)
        document.getElementById("expirationDate").innerText = "<?php echo htmlspecialchars($expirationDate); ?>";
    <?php endif; ?>

    // Event listener for renewing the plan
    document.getElementById("renewBtn").addEventListener("click", function() {
        document.getElementById("premiumExpiredModal").style.display = "none"; // Close the expired modal
        document.getElementById("paymentModal").style.display = "flex"; // Show the payment modal
    });

    // Event listener for the "Close" button in the expired plan modal
    document.getElementById("closeBtn").addEventListener("click", function() {
        // Show confirmation alert
        let confirmClose = confirm("Your subscription will be canceled and demoted to the Free plan. Do you want to proceed?");
        
        if (confirmClose) {
            // If the user confirms, update the plan to Free
            cancelSubscription();
            // Hide the modal
            document.getElementById("premiumExpiredModal").style.display = "none";
        }
    });

    // Event listener for the "Cancel" button in the payment modal
    document.getElementById("paymentCancelBtn").addEventListener("click", function() {
        // Close the payment modal and show the expired plan modal again
        document.getElementById("paymentModal").style.display = "none";
        document.getElementById("premiumExpiredModal").style.display = "flex"; // Show the expired plan modal
    });
});

// Function to cancel subscription (demote to Free plan)
function cancelSubscription() {
    // Make an AJAX request to update the user's plan to Free (send a request to PHP backend)
    fetch('process/index/cancelsubscription.php', {
        method: 'POST',
        body: JSON.stringify({ action: 'cancel' }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Your subscription has been canceled and your plan is now Free.');
        } else {
            // If there's an error, show it in an alert
            alert('Error: ' + (data.error || 'An unexpected error occurred.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error occurred. Please try again later.');
    });
}

// Close modals when the user clicks cancel or outside the modal
function closeModal() {
    document.getElementById("premiumExpiredModal").style.display = "none";
    document.getElementById("paymentModal").style.display = "none";
}

// Add this JavaScript to handle click prevention for locked books
document.querySelectorAll('.book-cover-container').forEach(function(container) {
    // Check if the container has the lock overlay
    if (container.querySelector('.lock-overlay')) {
        container.classList.add('disabled');
    }
});

</script>

</body>
</html>
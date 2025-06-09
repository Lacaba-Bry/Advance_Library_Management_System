<?php
session_start();  // Start the session at the top of the script
require_once('../../../backend/config/config.php');
include '../../../reusable/header.php';

// Define the ISBN and prepare the query
$isbn = '978-1-23-456798-2';
$stmt = $conn->prepare("SELECT * FROM books WHERE ISBN = ?");
$stmt->bind_param("s", $isbn);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if book exists
if (!$book) {
    die("Book not found.");
}

// Fetch the book details
$Book_ID = $book['Book_ID'];
$Plan_type = $book['Plan_type'];
$coverPath = "../../../Book/" . $Plan_type . "/Book_Cover/" . basename(htmlspecialchars($book['Book_Cover']));
$stock = $book['Stock'];

$userId = $_SESSION['user_id'] ?? null;


$stock = $book['Stock'];
$returnDate = $_GET['return_date'] ?? null;

// Get live vote count from the votes table
$voteCountStmt = $conn->prepare("SELECT COUNT(*) AS vote_count FROM votes WHERE Book_ID = ?");
$voteCountStmt->bind_param("i", $Book_ID);
$voteCountStmt->execute();
$voteResult = $voteCountStmt->get_result()->fetch_assoc();
$voteCount = $voteResult['vote_count'] ?? 0;
$voteCountStmt->close();

// Get read count (number of rentals and purchases)
$readCountStmt = $conn->prepare("
    SELECT COUNT(*) AS read_count
    FROM (
        -- Count rentals
        SELECT 1 FROM rent WHERE Book_ID = ? AND Account_ID = ? AND Status = 'ongoing' AND Return_Date > NOW()

        UNION ALL

        -- Count purchases
        SELECT 1 FROM transaction_book WHERE book_id = ? AND user_id = ?
    ) AS combined_read
");

$readCountStmt->bind_param("iiii", $Book_ID, $userId, $Book_ID, $userId);
$readCountStmt->execute();
$readResult = $readCountStmt->get_result()->fetch_assoc();
$readCount = $readResult['read_count'] ?? 0;
$readCountStmt->close();

if (isset($_GET['reservation_success']) && $_GET['reservation_success'] == 1) {
    // Alert (browser popup)
    echo "<script>alert('Reservation successful! You will be notified when the book becomes available.');</script>";

    // Alternatively, you can display this message inline:
    // echo "<div class='alert alert-success' role='alert'>Reservation successful! You will be notified when the book becomes available.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Preview: <?php echo htmlspecialchars($book['Title']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../../css/autogenerate/previewx.css">
  <script src="../../../javascript/generatescript.js"></script>
  <style>
      .vote-btn {
        background: none;
        border: none;
        opacity: 0.6;
      }
  </style>
</head>
<body>
<div class="book-preview">
 <div class="preview-header">
    <img src="<?php echo 'http://localhost/BryanCodeX/Book/' . $Plan_type . '/Book_Cover/' . basename(htmlspecialchars($book['Book_Cover'])); ?>" alt="Book Cover" class="book-cover">
    <div class="book-info">
      <h2 class="book-title"><?php echo htmlspecialchars($book['Title']); ?></h2>
      <div class="book-stats">
       <span><i class="fas fa-eye"></i> <strong><?= $readCount ?></strong> Reads</span>
  <button class="vote-btn" id="voteBtn" onclick="submitVote(<?= $book['Book_ID'] ?>, <?= $userId ?? 'null' ?>)">
    <i class="fas fa-star"></i>&nbsp;<strong id="voteCount"><?= $voteCount ?></strong>
</button>

</span>

        <span><i class="fas fa-list"></i> <strong>1</strong> Parts</span>
        <span><i class="fa-solid fa-book"></i> <strong><?= $stock ?></strong> Available</span>
      </div>
<div class="start-reading">
<?php
$canRead = false; // Initialize to false

if ($userId) {
    // Check if the user has purchased the book
    $purchasedStmt = $conn->prepare("SELECT 1 FROM transaction_book WHERE user_id = ? AND book_id = ?");
    $purchasedStmt->bind_param("ii", $userId, $Book_ID);
    $purchasedStmt->execute();
    $purchasedResult = $purchasedStmt->get_result();

    if ($purchasedResult->num_rows > 0) {
        $canRead = true; // User has purchased the book
    }

    $purchasedStmt->close();

    // If not purchased, check if the user has rented the book and the rental is ongoing
    if (!$canRead) {
        $checkStmt = $conn->prepare("SELECT 1 FROM rent WHERE Account_ID = ? AND Book_ID = ? AND Status = 'ongoing' AND Return_Date > NOW()");
        $checkStmt->bind_param("ii", $userId, $Book_ID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $canRead = true; // User has an active rental
        }

        $checkStmt->close();
    }
}
?>
<button
    class="start-btn"
    onclick="location.href='../../../Book/<?= $book['Plan_type'] ?>/Story/<?= $isbn ?>_story.php'"
    <?php echo ($canRead) ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'; ?>>
    ▶ Start Reading
</button>

<?php if (!$canRead && $userId): ?>
    <?php if ($book['Plan_type'] === 'Free' || $book['Plan_type'] === 'Premium'): ?>
        <div class="button-container">
             <input type="hidden" name="isbn" value="<?= $isbn ?>">
            <form method="post" action="reserve_rent_book.php">
                  <?php if ($stock > 0): ?>
                  <button class="btn rent-btn" type="button" onclick="openModal('rentModal')">Rent</button>
                  <?php else: ?>
                    <p style="color: #888;">This book is out of stock. You can only reserve it.</p>
                  <?php endif; ?>
                </form>
              <?php if ($stock === 0): ?>
                                      <!-- Reserve Button (Enabled when stock is 0) -->
                                      <form method="post" action="../../../process/admin/reserve_book.php">
                                          <input type="hidden" name="isbn" value="<?= $isbn ?>">
                                          <button class="btn reserve-btn" type="submit">Reserve</button>
                                      </form>
                                  <?php else: ?>
                                      <!-- Reserve Button (Disabled when stock is more than 0) -->
                                      <form method="post" action="../../../process/admin/reserve_book.php">
                                          <input type="hidden" name="isbn" value="<?= $isbn ?>">
                                          <button class="btn reserve-btn" type="submit" disabled style="opacity: 0.5; cursor: not-allowed;">Reserve</button>
                                      </form>
                                  <?php endif; ?>
                 <!-- Add to Cart Button (Always available) -->
                  <form method="POST" action="../../../process/Book/add_cart.php" id="addToCartForm">
                    <input type="hidden" name="book_id" value="<?= $book['Book_ID'] ?>">
                    <button type="submit" class="btn add-btn">Add to Cart</button>
                  </form>
            </div>

        <p style="color: #888;">Please rent or reserve this book to start reading.</p>
    <?php elseif ($book['Plan_type'] === 'Paid'): ?>
        <form method="post" action="../../../process/Book/paybook.php">
            <input type="hidden" name="isbn" value="<?= $isbn ?>">
            <input type="hidden" name="price" value="<?= $book['Price'] ?>">
            <button class="buy-btn" type="button" data-bs-toggle="modal" data-bs-target="#paymentModal">
        💳 Buy for $<?= $book['Price'] ?>
    </button>
        </form>
        <p style="color: #888;">Purchase required to read this book.</p>
    <?php endif; ?>
<?php elseif (!$userId): ?>
    <p style="color: #888;">Please log in to continue.</p>
<?php endif; ?>
</div>
</div>
</div>

  <div class="book-content">
    <h3>Story Snippet</h3>
    <p><?php echo nl2br(htmlspecialchars($book['Story_Snippet'])); ?></p>

    <h3>Description</h3>
    <p><?php echo nl2br(htmlspecialchars($book['Description'])); ?></p>
  </div>

<div class="reviews-section">
    <h3>Reviews</h3>

    <?php
    // Query to fetch reviews, user data, and plan data
    $reviewStmt = $conn->prepare("SELECT r.Review_Text, r.Created_At, u.Fullname, p.Plan_Name
                                  FROM reviews r
                                  JOIN profiles u ON r.Profile_ID = u.Profile_ID
                                  JOIN accountlist al ON u.Account_ID = al.Account_ID
                                  JOIN plans p ON al.Plan_ID = p.Plan_ID
                                  WHERE r.Book_ID = ?");
    $reviewStmt->bind_param("i", $book['Book_ID']);
    $reviewStmt->execute();
    $reviews = $reviewStmt->get_result();

    // Check if there are reviews and display them
    if ($reviews->num_rows > 0) {
        while ($review = $reviews->fetch_assoc()) {
            echo "<div class='review'>";
            echo "<strong>" . htmlspecialchars($review['Fullname']) . " (" . htmlspecialchars($review['Plan_Name']) . ")</strong>";
            echo "<p>" . nl2br(htmlspecialchars($review['Review_Text'])) . "</p>";
            echo "<small>" . htmlspecialchars($review['Created_At']) . "</small>";
            echo "</div>";
        }
    } else {
        // Message if no reviews exist
        echo "<div class='review'><strong>Anonymous</strong><p>Be the first to leave a review!</p></div>";
    }

    $reviewStmt->close();
    ?>

    <!-- Review Submission Form -->
    <h4>Leave a Review</h4>
    <?php if ($userId): ?>
        <form method="post" action="../../../process/index/submit_review.php">
            <input type="hidden" name="isbn" value="<?= $isbn ?>">
            <div class="form-group">
                <label for="reviewText">Your Review</label>
                <textarea name="reviewText" id="reviewText" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    <?php else: ?>
        <!-- Prompt to log in if user is not logged in -->
        <p style="color: #888;">Please log in to leave a review.</p>
    <?php endif; ?>
</div>
</div>

<div id="rentModal" class="modal fade" tabindex="-1" aria-labelledby="rentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rentModalLabel">Select Rent Duration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="../../../process/index/addrent.php">
          <input type="hidden" name="isbn" value="<?= $isbn ?>">  <!-- Hidden ISBN -->
          <label for="rentDuration">Choose a duration:</label>
          <select name="rentDuration" id="rentDuration" class="form-control">
            <option value="3">3 Days</option>
            <option value="7">7 Days</option>
            <option value="15">15 Days</option>
            <option value="30">30 Days</option>
          </select>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Rent Now</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal fade" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="paymentForm" method="POST" action="../../../process/Book/paybook.php">
          <input type="hidden" name="isbn" value="<?= $isbn ?>">
          <input type="hidden" name="price" value="<?= $book['Price'] ?>">

          <div class="form-group">
            <label for="cardNumber">Card Number</label>
            <input type="text" id="cardNumber" name="cardNumber" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="expiryDate">Expiry Date (MM/YY)</label>
            <input type="text" id="expiryDate" name="expiryDate" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" class="form-control" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Confirm Payment</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('paymentForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(this);
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

    console.log("Form data being sent to server:", formData);  // Log data to debug

    fetch('../../../process/Book/paybook.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);  // Display success message

            // Reload the page to show the updated state after payment
            window.location.reload(); // This will reload the current page
        } else {
            console.error('Payment failed:', data);  // Log error response
            alert(data.message);  // Display error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');  // Show generic error message
    });

    paymentModal.hide(); // Hide the payment modal
});

document.getElementById('addToCartForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission

    const formData = new FormData(this);

    // Send the request to add the book to the cart
    fetch('../../../process/Book/add_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and keep the user on the same page
            alert(data.message);
        } else {
            // Show the message if the book is already in the cart
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close the connection after all queries
$conn->close();
?>

<?php
require_once(__DIR__ . '/../backend/config/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php'); // Redirect to login if not logged in
    exit();
}

$userId = $_SESSION['user_id'];

// Prepare and execute the query to fetch rentals data
$stmt = $conn->prepare("SELECT r.Rent_ID, r.Rent_Date, r.Return_Date, r.Status, b.Title, b.Author 
                        FROM rentals r
                        JOIN books b ON r.Book_ID = b.Book_ID
                        WHERE r.Account_ID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$rentals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Rentals</title>
  <link rel="stylesheet" href="../../../css/autogenerate/styles.css">
</head>
<body>
  <main>
 <header class="header">
      <span class="logo-section"><span class="logo">Home</span></span>
      <div class="user-info">
        <div class="user-profile">
          <img src="./sample1.jpg" alt="Profile picture" class="profile-img">
          <span class="user-name">Bryan Lacaba</span>
        </div>
      </div>
    </header>

   

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Book Title</th>
            <th>Author</th>
            <th>Rent Date</th>
            <th>Return Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (count($rentals) > 0) {
              foreach ($rentals as $rental) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($rental['Title']) . "</td>";
                  echo "<td>" . htmlspecialchars($rental['Author']) . "</td>";
                  echo "<td>" . htmlspecialchars($rental['Rent_Date']) . "</td>";
                  echo "<td>" . ($rental['Return_Date'] ? htmlspecialchars($rental['Return_Date']) : 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($rental['Status']) . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No rentals found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>

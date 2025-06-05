<?php
require_once(__DIR__ . '/../backend/config/config.php');

// Fetch data from the transaction_book table
$query = "SELECT tb.paidbook_id, tb.user_id, tb.book_id, tb.purchase_date, tb.price, 
                 p.Fullname, p.avatar, b.Title, b.Author 
          FROM transaction_book tb
          JOIN profiles p ON tb.user_id = p.Account_ID
          JOIN books b ON tb.book_id = b.Book_ID";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Calculate Total Paid Books and Total Earnings
$total_transactions_query = "SELECT COUNT(*) AS total_paid_books, SUM(price) AS total_earnings FROM transaction_book";
$total_stmt = $conn->prepare($total_transactions_query);
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();

// Assign values to variables
$total_paid_books = $total_result['total_paid_books'] ?? 0;
$total_earnings = $total_result['total_earnings'] ?? 0;

// Fetch avatar from the 'profiles' table
$stmt = $conn->prepare("
    SELECT pr.Avatar
    FROM profiles pr
    WHERE pr.Account_ID = ?
");
$stmt->bind_param("i", $accountId);
$stmt->execute();
$stmt->bind_result($avatar); // Retrieve avatar
$stmt->fetch();
$stmt->close();

// Check if avatar is set; if not, use default avatar
if (!$avatar || empty($avatar)) {
    $avatar = 'image/profile/defaultprofile.jpg'; // Default image if avatar is not set
} else {
    // Ensure the avatar path is correct and safe
    $avatar = "image/profile/" . $avatar; // Adjust path to match your file structure
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Paid Book List | Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/adminheader.css">
  <style>
    :root {
        --primary-clr: #5e63ff;
        --bg-clr: #f9f9f9;
        --card-bg: #ffffff;
        --text-clr: #333;
        --muted-clr: #777;
        --border-clr: #e0e0e0;
        --success: #28a745;
        --danger: #dc3545;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-clr);
        color: var(--text-clr);
        margin: 0;
        padding: 0;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 20px 10px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .header-row h1 {
        margin: 0;
        font-size: 24px;
    }

    .right-controls {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .search-bar input {
        padding: 10px 15px;
        border: 1px solid var(--border-clr);
        border-radius: 6px;
        font-size: 14px;
    }

    .add-button {
        padding: 8px 16px;
        font-size: 14px;
        border: none;
        background-color: var(--primary-clr);
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .add-button:hover {
        background-color: #4e55e4;
    }

    .table-container {
        overflow-x: auto;
        margin: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: var(--card-bg);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    th, td {
        padding: 14px 18px;
        text-align: left;
        border-bottom: 1px solid var(--border-clr);
    }

    th {
        background: #f1f1f1;
        font-weight: 600;
    }

    tr:hover {
        background-color: #f9f9f9;
    }

    .badge.available {
        background-color: #e6f4ea;
        color: var(--success);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge.unavailable {
        background-color: #fdecea;
        color: var(--danger);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        th, td {
            font-size: 13px;
            padding: 10px;
        }

        .search-bar input {
            font-size: 13px;
        }
    }
    
   .summary-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  padding: 20px;
  margin-top: 10px;
}

.summary-cards .card {
  flex: 1 1 150px;
  background: var(--card-bg);
  border: 1px solid var(--border-clr);
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
  text-align: center;
}

.summary-cards .card h3 {
  font-size: 14px;
  color: var(--muted-clr);
  margin-bottom: 8px;
}

.summary-cards .card p {
  font-size: 20px;
  font-weight: 600;
  color: var(--text-clr);
  margin: 0;
}
  </style>
</head>
<body>
<main>
  <header class="header">
    <span class="logo-section">
      <span class="logo">Home</span>
    </span>
    <div class="user-info">
      <div class="user-profile">
        <img src="./sample1.jpg" alt="Admin profile" class="profile-img">
        <span class="user-name">Admin Panel</span>
      </div>
    </div>
  </header>

<div class="summary-cards">
      <div class="card">
        <h3>Total Paid Books</h3>
        <p><?php echo $total_paid_books; ?></p> <!-- Display dynamic total paid books -->
      </div>
      <div class="card">
        <h3>Total Earnings</h3>
        <p>$<?php echo number_format($total_earnings, 2); ?></p> <!-- Display dynamic total earnings -->
      </div>
    </div>

  <div class="header-row">
    <h1>Paid Books</h1>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>No.</th>
          <th>Profile</th>
          <th>Name</th>
          <th>Title</th>
          <th>Author</th>
          <th>Price</th>
          <th>Purchase Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
          <tr>
            <td><?= $row['paidbook_id'] ?></td>
            <td>
              <img src="<?php echo htmlspecialchars($avatar); ?>" alt="User Avatar" class="profile-avatar"  style="width: 30px; height: 30px; border-radius: 50%; margin-right: 5px;">
            </td>
            <td><?= htmlspecialchars($row['Fullname']) ?></td>
            <td><?= htmlspecialchars($row['Title']) ?></td>
            <td><?= htmlspecialchars($row['Author']) ?></td>
            <td>$<?= number_format($row['price'], 2) ?></td>
            <td><?= date('F j, Y', strtotime($row['purchase_date'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>

<?php
// Close the connection after all queries
$conn->close();
?>

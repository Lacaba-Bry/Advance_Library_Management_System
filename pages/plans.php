<?php
require_once(__DIR__ . '/../backend/config/config.php');

// Query to get the total number of transactions
$total_transactions_query = "SELECT COUNT(*) AS total_transactions FROM transaction_plan";
$total_transactions_result = $conn->query($total_transactions_query);
$total_transactions = 0; // Default value if the query fails

if ($total_transactions_result) {
    $row = $total_transactions_result->fetch_assoc();
    $total_transactions = $row['total_transactions'];
}

// Query to get the total earnings (sum of the amounts)
$total_earnings_query = "SELECT SUM(amount) AS total_earnings FROM transaction_plan WHERE payment_status = 'completed'";
$total_earnings_result = $conn->query($total_earnings_query);
$total_earnings = 0.00; // Default value if the query fails

if ($total_earnings_result) {
    $row = $total_earnings_result->fetch_assoc();
    $total_earnings = number_format($row['total_earnings'], 2);  // Format to 2 decimal places
}


$defaultProfileImage = 'image/profile/defaultprofile.jpg';  // Ensure this path is correct



// Query to fetch transaction details
$sql = "
    SELECT 
        t.transaction_id,
        a.Email as user_email,
        p.Fullname as user_fullname,
        IFNULL(p.Avatar, '$defaultProfileImage') as user_avatar,  -- Use default image if no avatar
        pl.Plan_Name as plan_name,
        t.amount,
        t.payment_status,
        t.transaction_date
    FROM 
        transaction_plan t
    JOIN 
        accountlist a ON t.account_id = a.Account_ID
    JOIN 
        profiles p ON a.Account_ID = p.Account_ID
    JOIN 
        plans pl ON t.plan_id = pl.Plan_ID
    ORDER BY t.transaction_date DESC
";


$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transactions | Admin Panel</title>
  <link rel="stylesheet" href="css/adminheader.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
      --warning: #ffc107;
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

@media (max-width: 600px) {
  .summary-cards {
    flex-direction: column;
  }
}

    .header-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      padding: 20px;
    }

    .header-row h1 {
      font-size: 24px;
      color: var(--text-clr);
    }

    .table-container {
      overflow-x: auto;
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

    .badge {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
    }

    .badge.success {
      background-color: #e6f4ea;
      color: var(--success);
    }

    .badge.failed {
      background-color: #fdecea;
      color: var(--danger);
    }

    .badge.pending {
      background-color: #fff4e5;
      color: var(--warning);
    }

    @media (max-width: 600px) {
      th, td {
        font-size: 13px;
        padding: 10px;
      }
    }
  </style>
  </style>
</head>
<body>
  <main>
    <header class="header">
        <span class="logo-section">
          <span class="logo">Home</span>
        </span>
        <div class="search-bar">
          <input type="text" placeholder="Search ..." aria-label="Search">
        </div>
        <div class="user-info">
          <div class="user-profile">
            <img src="./sample1.jpg" alt="Profile picture of Bryan Lacaba" class="profile-img">
            <span class="user-name">Bryan Lacaba</span>
          </div>
        </div>
    </header>
<!-- Summary Cards (Total Transactions and Earnings) -->
    <div class="summary-cards">
      <div class="card">
        <h3>Total Transactions</h3>
        <p><?php echo $total_transactions; ?></p> <!-- Display dynamic total transactions -->
      </div>
      <div class="card">
        <h3>Total Earnings</h3>
        <p>$<?php echo $total_earnings; ?></p> <!-- Display dynamic total earnings -->
      </div>
    </div>
    <!-- Table Container with Transactions -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Profile</th>
            <th>Plan</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Loop through the results and display each transaction
          while ($row = $result->fetch_assoc()) {
              $transaction_id = $row['transaction_id'];
              $user_fullname = $row['user_fullname'];
              $plan_name = $row['plan_name'];
              $amount = number_format($row['amount'], 2);  // Format amount to 2 decimal places
              $payment_status = $row['payment_status'];
              $transaction_date = $row['transaction_date'];

              // Determine the badge class based on payment status
              $badge_class = '';
              switch ($payment_status) {
                  case 'completed':
                      $badge_class = 'success';
                      break;
                  case 'pending':
                      $badge_class = 'pending';
                      break;
                  case 'failed':
                      $badge_class = 'failed';
                      break;
              }
              ?>
              <tr>
                <td><?= $transaction_id ?></td>
               <td><?= $user_fullname ?></td>
                <td><?= $plan_name ?></td>
                <td><?= $transaction_date ?></td>
                <td>$<?= $amount ?></td>
                <td><span class="badge <?= $badge_class ?>"><?= ucfirst($payment_status) ?></span></td>
              </tr>
              <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>

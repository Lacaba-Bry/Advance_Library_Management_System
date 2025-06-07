<?php
require_once(__DIR__ . '/../backend/config/config.php');

// Fetch customer count (number of members)
$customerCountQuery = $conn->prepare("SELECT COUNT(*) FROM register");
$customerCountQuery->execute();
$customerCountResult = $customerCountQuery->get_result();
$customerCount = $customerCountResult->fetch_row()[0];

// Fetch book purchase count (number of purchased books)
$bookPurchaseCountQuery = $conn->prepare("SELECT COUNT(*) FROM transaction_book WHERE user_id = ?");
$bookPurchaseCountQuery->bind_param("i", $accountId);
$bookPurchaseCountQuery->execute();
$bookPurchaseCountResult = $bookPurchaseCountQuery->get_result();
$bookPurchaseCount = $bookPurchaseCountResult->fetch_row()[0];

// Prepare and execute the query
$revenueQuery = $conn->prepare("
    SELECT 
        SUM(tb.price) AS book_sales_revenue, 
        IFNULL(SUM(tp.amount), 0) AS plan_revenue
    FROM transaction_book tb
    LEFT JOIN user_plans up ON tb.user_id = up.Account_ID
    LEFT JOIN transaction_plan tp ON up.Account_ID = tp.account_id
    WHERE tb.user_id = ?
");
$revenueQuery->bind_param("i", $accountId);
$revenueQuery->execute();
$revenueResult = $revenueQuery->get_result();

// Fetch the revenue data
$revenueData = $revenueResult->fetch_assoc();
$bookSalesRevenue = $revenueData['book_sales_revenue'];
$planRevenue = $revenueData['plan_revenue'];

// Calculate total revenue
$totalRevenue = $bookSalesRevenue + $planRevenue;

// Optionally, format the revenue as a currency value
$totalRevenueFormatted = number_format($totalRevenue, 2);



// Calculate growth percentage
$previousRevenueQuery = $conn->prepare("SELECT SUM(price) FROM transaction_book WHERE purchase_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
$previousRevenueQuery->execute();
$previousRevenueResult = $previousRevenueQuery->get_result();
$previousRevenue = $previousRevenueResult->fetch_row()[0];

$growth = (($totalRevenue - $previousRevenue) / $previousRevenue) * 100;

$topSellingBooksQuery = $conn->prepare("
    SELECT b.Title, b.price, COUNT(tb.book_id) as sales_count, SUM(tb.price) as total_sales
    FROM transaction_book tb
    JOIN books b ON tb.book_id = b.Book_ID
    WHERE tb.purchase_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
    GROUP BY tb.book_id
    ORDER BY sales_count DESC LIMIT 5
");

$topSellingBooksQuery->execute();
$topSellingBooksResult = $topSellingBooksQuery->get_result();

// Query to get current week's revenue by day
$currentWeekQuery = $conn->prepare("
    SELECT DAYOFWEEK(purchase_date) AS day_of_week, SUM(price) AS total_revenue
    FROM transaction_book
    WHERE purchase_date >= CURDATE() - INTERVAL (WEEKDAY(CURDATE()) + 1) DAY
    GROUP BY DAYOFWEEK(purchase_date)
    ORDER BY DAYOFWEEK(purchase_date);
");
$currentWeekQuery->execute();
$currentWeekResult = $currentWeekQuery->get_result();
$currentWeekRevenue = [];
while ($row = $currentWeekResult->fetch_assoc()) {
    $currentWeekRevenue[$row['day_of_week']] = $row['total_revenue'];
}

// Query to get previous week's revenue by day
$previousWeekQuery = $conn->prepare("
    SELECT DAYOFWEEK(purchase_date) AS day_of_week, SUM(price) AS total_revenue
    FROM transaction_book
    WHERE purchase_date >= CURDATE() - INTERVAL (WEEKDAY(CURDATE()) + 1 + 7) DAY
      AND purchase_date < CURDATE() - INTERVAL (WEEKDAY(CURDATE()) + 1) DAY
    GROUP BY DAYOFWEEK(purchase_date)
    ORDER BY DAYOFWEEK(purchase_date);
");
$previousWeekQuery->execute();
$previousWeekResult = $previousWeekQuery->get_result();
$previousWeekRevenue = [];
while ($row = $previousWeekResult->fetch_assoc()) {
    $previousWeekRevenue[$row['day_of_week']] = $row['total_revenue'];
}

// Prepare data for chart (Daily revenue data for both weeks)
$currentWeekData = [];
$previousWeekData = [];

for ($i = 1; $i <= 7; $i++) {
    $currentWeekData[] = isset($currentWeekRevenue[$i]) ? $currentWeekRevenue[$i] : 0;
    $previousWeekData[] = isset($previousWeekRevenue[$i]) ? $previousWeekRevenue[$i] : 0;
}

// Convert to JSON for JavaScript
$currentWeekDataJson = json_encode($currentWeekData);
$previousWeekDataJson = json_encode($previousWeekData);

// Format revenue for display in chart-info
$currentWeekTotalRevenue = array_sum($currentWeekData);
$previousWeekTotalRevenue = array_sum($previousWeekData);
$currentWeekTotalRevenueFormatted = number_format($currentWeekTotalRevenue, 2);
$previousWeekTotalRevenueFormatted = number_format($previousWeekTotalRevenue, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/adminheader.css">
   <link rel="stylesheet" href="css/page/dashboard.css">
  <!-- Bootstrap CSS & Icons -->
  <title>Document</title>
 <style>
 
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
            <img src="./sample1.jpg" alt="Profile picture of Arafat Hossain" class="profile-img">
            <span class="user-name">Bryan Lacaba</span>

          </div>
    </div>
  </header>



    <main class="dashboard-container">
        <!-- LEFT: 2×2 metrics -->
        <div class="metrics-container">
            <div class="metric">
                <div class="metric-header">
                    member <span class="icon">👥</span>
                </div>
                <div class="metric-value"><?php echo $customerCount; ?></div>
                <div class="metric-change positive">+5.27% Since last month</div>
            </div>
            <div class="metric">
                <div class="metric-header">
                    Orders <span class="icon">🛒</span>
                </div>
                <div class="metric-value"><?php echo $bookPurchaseCount; ?></div>
                <div class="metric-change negative">-1.08% Since last month</div>
            </div>
               <div class="metric">
                  <div class="metric-header">
                      Revenue <span class="icon">$</span>
                  </div>
                  <div class="metric-value"><?php echo "$" . $totalRevenueFormatted; ?></div>
                  <div class="metric-change negative">-7.00% Since last month</div>
              </div>
            <div class="metric">
                <div class="metric-header">
                    Growth <span class="icon">↗️</span>
                </div>
                <div class="metric-value"><?php echo "+$growth%"; ?></div>
                <div class="metric-change positive">+4.87% Since last month</div>
            </div>
        </div>

        <!-- RIGHT: Revenue chart card -->
        <div class="chart-section">
            <div class="chart-header">
                <span class="chart-title">REVENUE</span>
                    <div class="chart-info">
                        <div>Current Week: <strong>$<?php echo $currentWeekTotalRevenueFormatted; ?></strong></div>
                        <div>Previous Week: <strong>$<?php echo $previousWeekTotalRevenueFormatted; ?></strong></div>
                    </div>
            </div>
            <canvas id="revenueChart"></canvas>
            <div class="chart-footer">
                <span>Today's Earnings: $2,562.30</span>
                <button>View Statements</button>
            </div>
        </div>

      <div class="side-by-side">
    <!-- Right: Recent Activity -->
  <div class="col-half">
    <div class="card p-4">
      <h5><i class="bi bi-clock-history"></i> Recent Activity</h5>
      <ul class="activity-list mt-3">
        <li><span class="activity-icon">🛍️</span> You sold an item - <strong>Paul Burgess</strong> purchased <em>Hyper - Admin Dashboard</em><br><small class="text-muted">5 minutes ago</small></li>
        <li><span class="activity-icon">📦</span> Product on the Bootstrap Market - <strong>Dave Gamache</strong> added <em>Admin Dashboard</em><br><small class="text-muted">30 minutes ago</small></li>
        <li><span class="activity-icon">💬</span> <strong>Robert Delaney</strong> sent you a message: "Are you there?"<br><small class="text-muted">2 hours ago</small></li>
        <li><span class="activity-icon">📁</span> <strong>Audrey Tobey</strong> uploaded a photo "Error.jpg"<br><small class="text-muted">14 hours ago</small></li>
        <li><span class="activity-icon">🛍️</span> You sold an item - <strong>Paul Burgess</strong> purchased <em>Hyper - Admin Dashboard</em><br><small class="text-muted">16 hours ago</small></li>
      </ul>
    </div>
  </div>
</div>

        <!-- Left: Top Selling Books -->
        <div class="col-half">
            <div class="card p-4">
                <h5><i class="bi bi-book"></i> Top Selling Books (Monthly)</h5>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Status</th>
                            <th>Purchase</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $topSellingBooksResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['Title']); ?></td>
                                <td>$<?php echo htmlspecialchars($book['price']); ?></td>
                                <td><?php echo htmlspecialchars($book['sales_count']); ?></td>
                                <td>$<?php echo number_format($book['total_sales'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</main>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], // Days of the week
                datasets: [
                    {
                        label: 'Current Week',
                        data: <?php echo $currentWeekDataJson; ?>,  // Current week revenue data
                        borderColor: 'rgba(0,123,255,1)', 
                        backgroundColor: 'rgba(0,123,255,0.2)',
                        fill: true, 
                        tension: 0.4
                    },
                    {
                        label: 'Previous Week',
                        data: <?php echo $previousWeekDataJson; ?>,  // Previous week revenue data
                        borderColor: 'rgba(40,167,69,1)', 
                        backgroundColor: 'rgba(40,167,69,0.2)',
                        fill: true, 
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
</body>
</html>

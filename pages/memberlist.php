<?php
require_once(__DIR__ . '/../backend/config/config.php');

// *** SUMMARY CARDS QUERIES ***

// Total Members
$totalMembersQuery = "SELECT COUNT(*) AS total FROM register";
$totalMembersResult = $conn->query($totalMembersQuery);
$totalMembers = $totalMembersResult->fetch_assoc()['total'];

// Last 7 Days
$last7DaysQuery = "SELECT COUNT(*) AS total FROM register WHERE Date_Created >= DATE(NOW()) - INTERVAL 7 DAY";
$last7DaysResult = $conn->query($last7DaysQuery);
$last7Days = $last7DaysResult->fetch_assoc()['total'];

// Last 30 Days
$last30DaysQuery = "SELECT COUNT(*) AS total FROM register WHERE Date_Created >= DATE(NOW()) - INTERVAL 30 DAY";
$last30DaysResult = $conn->query($last30DaysQuery);
$last30Days = $last30DaysResult->fetch_assoc()['total'];

// Membership Counts
$freeMembersQuery = "SELECT COUNT(*) AS total FROM accountlist al JOIN register r ON al.Register_ID = r.Register_ID JOIN plans p ON al.Plan_ID = p.Plan_ID WHERE p.Plan_Name = 'Free'";
$freeMembersResult = $conn->query($freeMembersQuery);
$freeMembers = $freeMembersResult->fetch_assoc()['total'];

$premiumMembersQuery = "SELECT COUNT(*) AS total FROM accountlist al JOIN register r ON al.Register_ID = r.Register_ID JOIN plans p ON al.Plan_ID = p.Plan_ID WHERE p.Plan_Name = 'Premium'";
$premiumMembersResult = $conn->query($premiumMembersQuery);
$premiumMembers = $premiumMembersResult->fetch_assoc()['total'];

$vipMembersQuery = "SELECT COUNT(*) AS total FROM accountlist al JOIN register r ON al.Register_ID = r.Register_ID JOIN plans p ON al.Plan_ID = p.Plan_ID WHERE p.Plan_Name = 'VIP'";
$vipMembersResult = $conn->query($vipMembersQuery);
$vipMembers = $vipMembersResult->fetch_assoc()['total'];

// *** MEMBER LIST QUERY ***

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$membershipFilter = isset($_GET['membership']) ? $_GET['membership'] : 'all';

$query = "SELECT
            r.Register_ID,
            r.Fullname,
            r.Email,
            p.Plan_Name,
            r.Date_Created,
            up.Expiration_Date
          FROM
            register r
          JOIN
            accountlist a ON r.Register_ID = a.Register_ID
          JOIN
            plans p ON a.Plan_ID = p.Plan_ID
          LEFT JOIN
            user_plans up ON a.Account_ID = up.Account_ID  -- Using LEFT JOIN to handle potential null expiration dates
          WHERE
            r.Fullname LIKE ? OR r.Email LIKE ?";

if ($membershipFilter !== 'all') {
    $query .= " AND p.Plan_Name = ?";  // Filter by Plan_Name
}

$stmt = $conn->prepare($query);
$searchParam = "%" . $searchTerm . "%";

if ($membershipFilter !== 'all') {
    $stmt->bind_param("sss", $searchParam, $searchParam, $membershipFilter);
} else {
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

try {
    $stmt->execute();
    $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<p style='color: red;'>An error occurred while retrieving members. Please contact the administrator.</p>";
    $members = [];
}


// Function to get membership status
function getMembershipStatus($expirationDate, $planName) {
    if ($planName === 'VIP') {
        return 'Permanent';  // For VIP members, status is 'Permanent'
    }

    if ($expirationDate === null) {
        return 'No Expiration'; // Or 'Lifetime', 'Free', etc. Adapt this to your logic.
    }

    $now = new DateTime();
    $expiration = new DateTime($expirationDate);

    if ($expiration >= $now) {  // Changed from "<" to ">="
        return 'Active';
    } else {
        return 'Expired';
    }
}

// Function to get badge class based on membership status
function getBadgeClass($status) {
    if ($status === 'Active') {
        return 'success';
    } elseif ($status === 'Expired') {
        return 'danger';
    } elseif ($status === 'Permanent') {
        return 'secondary';  // For VIP members
    } elseif ($status === 'No Expiration') {
        return 'no-expiration';  // For 'No Expiration' status
    } else {
        return 'secondary';  // For 'No Expiration' or other cases
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Member List | Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    --table-header-bg: #f8f9fa;
    --table-row-hover-bg: #e9ecef;
    --table-border-color: #dee2e6;
  }

  body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #f4f6ff, #eaefff);
  color: var(--text-clr);
  margin: 0;
  padding: 0;
  line-height: 1.6;
  min-height: 100vh;
}

main {
  padding: 20px;
}

.page-header {
  background-color: var(--primary-clr);
  color: white;
  padding: 16px 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  font-size: 20px;
  font-weight: 600;
  border-radius: 0 0 12px 12px;
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
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .summary-cards .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
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

  .search-bar input,
  .sort-dropdown select {
    padding: 10px 15px;
    padding-right: 30px;
    border: 1px solid var(--border-clr);
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
    outline: none;
  }

  .search-bar input:focus,
  .sort-dropdown select:focus {
    border-color: var(--primary-clr);
    box-shadow: 0 0 0 0.2rem rgba(94, 99, 255, 0.2);
  }

  .table-container {
    overflow-x: auto;
    padding: 0 20px 20px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background: var(--card-bg);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--table-border-color);
  }

  th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid var(--table-border-color);
    font-size: 14px;
  }

  th {
    background: var(--table-header-bg);
    font-weight: 600;
    color: var(--text-clr);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-size: 13px;
  }

  tbody tr {
    transition: background-color 0.2s ease;
  }

  tbody tr:hover {
    background-color: var(--table-row-hover-bg);
  }

  .table-active {
    background-color: #dfe7ff !important;
  }

  .badge {
    padding: 5px 10px;
    border-radius: 0.25rem;
    font-size: 0.8rem;
    font-weight: 500;
    display: inline-block;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
  }

  .badge::before {
    margin-right: 6px;
    display: inline-block;
  }

  .bg-success {
    background: linear-gradient(135deg, #7d6ce3 0%, #4e3bcf 100%) !important;
    color: white;
    box-shadow: 0px 4px 12px rgba(78, 59, 207, 0.3);
  }
  .bg-success::before { content: "✔️"; }

  .bg-danger {
    background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%) !important;
    color: white;
    box-shadow: 0px 4px 12px rgba(244, 67, 54, 0.3);
  }
  .bg-danger::before { content: "⛔"; }

  .bg-secondary {
    background: linear-gradient(135deg, #ffd700 0%, #c49b28 100%) !important;
    color: white;
    box-shadow: 0px 4px 12px rgba(196, 155, 40, 0.3);
  }
  .bg-secondary::before { content: "👑"; }

  .bg-no-expiration {
    background-color: grey !important;
    color: white !important;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
  }
  .bg-no-expiration::before { content: "📅"; }

  .bg-success:hover, .bg-danger:hover, .bg-secondary:hover, .bg-no-expiration:hover {
    transform: scale(1.05);
    box-shadow: 0px 6px 18px rgba(0, 0, 0, 0.1);
  }

  .profile-image {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
    vertical-align: middle;
    border: 1px solid var(--border-clr);
  }

  .profile-cell {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  td {
    box-shadow: inset 0 0 0 9999px var(--card-bg);
    clip-path: inset(0 0 0 0);
  }

  /* Responsive tweak for controls */
  @media (max-width: 768px) {
    .header-row {
      flex-direction: column;
      align-items: flex-start;
    }
    .d-flex.flex-wrap {
      width: 100%;
      justify-content: space-between;
    }
  }

  .pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  padding: 24px 0;
  flex-wrap: wrap;
  margin-top: auto;
}

.pagination a {
  padding: 8px 14px;
  text-decoration: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: var(--primary-clr);
  background-color: white;
  border: 1px solid var(--primary-clr);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
}

.pagination a:hover:not(.active):not(.disabled) {
  background-color: var(--primary-clr);
  color: white;
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.pagination a.active {
  background-color: var(--primary-clr);
  color: white;
  border: 1px solid var(--primary-clr);
  pointer-events: none;
}

.pagination a.disabled {
  color: #bbb;
  border-color: #ccc;
  background-color: #f1f1f1;
  pointer-events: none;
  box-shadow: none;
}

.table-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 60vh;
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
            <img src="./sample1.jpg" alt="Profile picture of Arafat Hossain" class="profile-img">
            <span class="user-name">Bryan Lacaba</span>

          </div>
    </div>
  </header>

<div class="card">
      <div class="summary-cards">
  <div class="card">
    <h3>Total Members</h3>
    <p><?php echo $totalMembers; ?></p>
  </div>
  <div class="card">
    <h3>Last 7 Days</h3>
    <p><?php echo $last7Days; ?></p>
  </div>
  <div class="card">
    <h3>Last 30 Days</h3>
    <p><?php echo $last30Days; ?></p>
  </div>
  <div class="card">
    <h3>Free</h3>
    <p><?php echo $freeMembers; ?></p>
  </div>
  <div class="card">
    <h3>Premium</h3>
    <p><?php echo $premiumMembers; ?></p>
  </div>
  <div class="card">
    <h3>VIP</h3>
    <p><?php echo $vipMembers; ?></p>
  </div>
</div>
</div>



<div class="header-row">
    <h1>Member List</h1>
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-end">
        <form method="get" action="memberlist.php" class="search-bar">
            <div class="input-group">
                <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($searchTerm); ?>" class="form-control">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <form method="get" action="memberlist.php" class="sort-dropdown">
            <select name="membership" onchange="this.form.submit()" class="form-select">
                <option value="all" <?php if ($membershipFilter == 'all') echo 'selected'; ?>>All Memberships</option>
                <option value="Free" <?php if ($membershipFilter == 'Free') echo 'selected'; ?>>Free</option>
                <option value="Premium" <?php if ($membershipFilter == 'Premium') echo 'selected'; ?>>Premium</option>
                <option value="VIP" <?php if ($membershipFilter == 'VIP') echo 'selected'; ?>>VIP</option>
            </select>
        </form>
    </div>
</div>

<div class="table-wrapper">
<div class="table-container">
  <table id="memberTable" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Profile</th>
        <th>Name</th>
        <th>Email</th>
        <th>Membership</th>
        <th>Date_Created</th>
        <th>Membership Status</th>
      </tr>
    </thead>
    <tbody>


      <?php
       $defaultProfileImage = 'image/profile/defaultprofile.jpg'; //  Corrected image path
       foreach ($members as $index => $member) {
    $status = getMembershipStatus($member['Expiration_Date'], $member['Plan_Name']); // Pass Plan_Name
    $badgeClass = getBadgeClass($status); // Get badge class based on status
    $profileLink = 'profile.php?id=' . urlencode($member['Register_ID']);

    echo '<tr>';
    echo '<td>' . ($index + 1) . '</td>'; // The index number
    echo '<td><a href="index/' . htmlspecialchars($profileLink) . '" class="profile-cell"><img src="' . htmlspecialchars($defaultProfileImage) . '" alt="Profile" class="profile-image"></a></td>';
    echo '<td>' . htmlspecialchars($member['Fullname']) . '</td>';
    echo '<td>' . htmlspecialchars($member['Email']) . '</td>';
    echo '<td>' . htmlspecialchars($member['Plan_Name']) . '</td>';
    echo '<td>' . htmlspecialchars($member['Date_Created']) . '</td>';
    echo '<td><span class="badge bg-' . $badgeClass . '">' . $status . '</span></td>';
    echo '</tr>';
}

?>
    </tbody>
  </table>
</div>

<div class="pagination" id="pagination"></div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('memberTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const paginationContainer = document.getElementById('pagination');
    const rowsPerPage = 5; // You can change this to 10 or any number
    let currentPage = 1;

    function displayTablePage(page) {
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;

      rows.forEach((row, index) => {
        row.style.display = (index >= start && index < end) ? '' : 'none';
      });

      updatePaginationLinks(page);
    }

    function updatePaginationLinks(activePage) {
      const pageCount = Math.ceil(rows.length / rowsPerPage);
      paginationContainer.innerHTML = '';

      if (pageCount <= 1) return;

      const prev = document.createElement('a');
      prev.href = '#';
      prev.innerHTML = '&laquo;';
      prev.className = activePage === 1 ? 'disabled' : '';
      prev.onclick = () => {
        if (activePage > 1) displayTablePage(activePage - 1);
      };
      paginationContainer.appendChild(prev);

      for (let i = 1; i <= pageCount; i++) {
        const pageLink = document.createElement('a');
        pageLink.href = '#';
        pageLink.textContent = i;
        pageLink.className = i === activePage ? 'active' : '';
        pageLink.onclick = () => displayTablePage(i);
        paginationContainer.appendChild(pageLink);
      }

      const next = document.createElement('a');
      next.href = '#';
      next.innerHTML = '&raquo;';
      next.className = activePage === pageCount ? 'disabled' : '';
      next.onclick = () => {
        if (activePage < pageCount) displayTablePage(activePage + 1);
      };
      paginationContainer.appendChild(next);
    }

    // Initial load
    displayTablePage(currentPage);
  });
</script>

</body>
</html>
<?php
require_once(__DIR__ . '/../backend/config/config.php');

// Fetch the user activity log data from the database
$query = "SELECT name, email, role, status, login_time FROM user_activity_log ORDER BY login_time DESC LIMIT 10"; // Fetch last 10 logs
$result = $conn->query($query);

// Check if there are results
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel | User Activity Log</title>
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

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg-clr);
    }

    h1 {
      margin-top: 30px;
      margin-bottom: 30px;
    }

    table {
      background-color: var(--card-bg);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    th, td {
      vertical-align: middle !important;
    }

    .badge.active {
      background-color: #e6f4ea;
      color: var(--success);
    }

    .badge.inactive {
      background-color: #fdecea;
      color: var(--danger);
    }

    .badge.admin {
      background-color: var(--primary-clr);
      color: white;
    }

    .badge.user {
      background-color: #6c757d;
      color: white;
    }
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

  <h1>User Activity Log</h1>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="thead-light">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Login Time</th>
          <th>Activity</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><span class="badge <?php echo strtolower($row['role']); ?>"><?php echo htmlspecialchars($row['role']); ?></span></td>
            <td><span class="badge <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
            <td><?php echo htmlspecialchars($row['login_time']); ?></td>
            <td>Active</td> <!-- You can modify activity based on your logic -->
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
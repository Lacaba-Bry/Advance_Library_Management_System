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
      <tbody id="logTableBody">
        <!-- Rows inserted by JavaScript -->
      </tbody>
    </table>
  </div>
</main>

<script>
  const logData = [
    {
      name: "Arafat Hossain",
      email: "arafat@example.com",
      role: "Admin",
      status: "Active",
      loginTime: new Date().toLocaleTimeString(),
      activity: "Browsing"
    },
    {
      name: "Jane Smith",
      email: "jane@example.com",
      role: "User",
      status: "Inactive",
      loginTime: new Date(Date.now() - 600000).toLocaleTimeString(), // 10 minutes ago
      activity: "Idle"
    },
    {
      name: "Michael Doe",
      email: "michael@example.com",
      role: "User",
      status: "Active",
      loginTime: new Date(Date.now() - 300000).toLocaleTimeString(), // 5 minutes ago
      activity: "Reading"
    }
  ];

  function renderTable(data) {
    const tbody = document.getElementById("logTableBody");
    tbody.innerHTML = "";
    data.forEach(user => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${user.name}</td>
        <td>${user.email}</td>
        <td><span class="badge ${user.role.toLowerCase()}">${user.role}</span></td>
        <td><span class="badge ${user.status.toLowerCase()}">${user.status}</span></td>
        <td>${user.loginTime}</td>
        <td>${user.activity}</td>
      `;
      tbody.appendChild(row);
    });
  }

  renderTable(logData);
</script>

</body>
</html>

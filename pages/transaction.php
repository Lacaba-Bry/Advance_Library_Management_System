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


<div class="summary-cards">
  <div class="card">
    <h3>Total Transactions</h3>
    <p>123</p> <!-- Replace with dynamic or updated value -->
  </div>
  <div class="card">
    <h3>Total Earnings</h3>
    <p>$725.35</p> <!-- Replace with dynamic or updated value -->
  </div>
</div>


    <div class="header-row">
      <h1>Transaction History</h1>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>User</th>
            <th>Plan</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>TX001</td>
            <td>Jane Doe</td>
            <td>Premium / Monthly</td>
            <td>2025-05-01</td>
            <td>$4.99</td>
            <td><span class="badge success">Success</span></td>
          </tr>
          <tr>
            <td>TX002</td>
            <td>John Smith</td>
            <td>VIP / Permanent</td>
            <td>2025-05-03</td>
            <td>$49.99</td>
            <td><span class="badge pending">Pending</span></td>
          </tr>
          <tr>
            <td>TX003</td>
            <td>Emily Johnson</td>
            <td>Premium / Monthly</td>
            <td>2025-05-05</td>
            <td>$4.99</td>
            <td><span class="badge failed">Failed</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>

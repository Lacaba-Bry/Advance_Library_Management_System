<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/adminheader.css">
  <!-- Bootstrap CSS & Icons -->
  <title>Document</title>
 <style>
  body {
    background-color: #f8f9fc;
    font-family: 'Segoe UI', sans-serif;
  }

  .header {
    background-color: #fff;
    padding: 1rem 2rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
   /* ------------------------------------------------
       1) Wrapper: two-column layout
    ------------------------------------------------ */
    .dashboard-container {
      display: grid;
      grid-template-columns: 1fr 2fr;       /* left:metrics, right:chart */
      gap: 20px;
      padding: 20px;
    }

    /* ------------------------------------------------
       2) Left side: 2×2 grid of metrics
    ------------------------------------------------ */
    .metrics-container {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
    .metric {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .metric:hover { transform: translateY(-5px); }
    .metric-header { font-size: 14px; color: #6c757d; margin-bottom: 10px; }
    .metric-value  { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
    .metric-change { font-size: 12px; }
    .metric-change.positive { color: #28a745; }
    .metric-change.negative { color: #dc3545; }
    .icon { margin-left: 8px; font-size: 18px; color: #007bff; }

    /* ------------------------------------------------
       3) Right side: standalone chart card
    ------------------------------------------------ */
    .chart-section {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%; 
    }
    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .chart-title { font-size: 16px; font-weight: bold; }
    .chart-info  { font-size: 14px; color: #6c757d; }
    .chart-footer {
      display: flex; justify-content: space-between; align-items: center;
      margin-top: 10px; font-size: 14px;
    }
    .chart-footer button {
      background-color: #007bff; color: #fff; border: none;
      padding: 6px 12px; border-radius: 5px; cursor: pointer;
    }
    .chart-footer button:hover { background-color: #0056b3; }

    @media (max-width: 900px) {
      .dashboard-container {
        grid-template-columns: 1fr;  /* stack on mobile */
      }
    }
/* Container for left/right layout */
.side-by-side {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}
.col-left {
  flex: 0 0 75%;
  min-width: 200px;
}

.col-right {
  flex: 0 0 25%;
  min-width: 150px;
}

/* Card styling */
.card {
  background-color: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.card h5 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Table */
.table {
  width: 100%;
  border-collapse: collapse;
}

.table thead th {
  font-weight: 600;
  font-size: 14px;
  color: #444;
  border-bottom: 1px solid #e5e5e5;
  padding-bottom: 10px;
}

.table tbody td {
  font-size: 14px;
  color: #333;
  padding: 12px 8px;
  border-bottom: 1px solid #f0f0f0;
}

/* Activity list */
.activity-list {
  list-style: none;
  padding-left: 0;
  margin: 0;
}

.activity-list li {
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
}

.activity-list li:last-child {
  border-bottom: none;
}

.activity-icon {
  margin-right: 8px;
  font-size: 16px;
}

.text-muted {
  color: #6c757d;
  font-size: 12px;
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
      <div class="notifications">
        <span class="notification-icon">&#128276;</span>
        <span class="notification-count">12</span>
      </div>
      <div class="user-profile">
        <img src="profile-pic.jpg" alt="Profile picture of Arafat Hossain" class="profile-img">
        <span class="user-name">Arafat Hossain</span>
        <span class="user-role">Librarian</span>
      </div>
    </div>
  </header>

 <main>
   <main class="dashboard-container">
    <!-- LEFT: 2×2 metrics -->
    <div class="metrics-container">
      <div class="metric">
        <div class="metric-header">
          Customers <span class="icon">👥</span>
        </div>
        <div class="metric-value">36,254</div>
        <div class="metric-change positive">+5.27% Since last month</div>
      </div>
      <div class="metric">
        <div class="metric-header">
          Orders   <span class="icon">🛒</span>
        </div>
        <div class="metric-value">5,543</div>
        <div class="metric-change negative">-1.08% Since last month</div>
      </div>
      <div class="metric">
        <div class="metric-header">
          Revenue  <span class="icon">$</span>
        </div>
        <div class="metric-value">$6,254</div>
        <div class="metric-change negative">-7.00% Since last month</div>
      </div>
      <div class="metric">
        <div class="metric-header">
          Growth   <span class="icon">↗️</span>
        </div>
        <div class="metric-value">+30.56%</div>
        <div class="metric-change positive">+4.87% Since last month</div>
      </div>
    </div>

    <!-- RIGHT: Revenue chart card -->
    <div class="chart-section">
      <div class="chart-header">
        <span class="chart-title">REVENUE</span>
        <div class="chart-info">
          <div>Current Week: <strong>$58,254</strong></div>
          <div>Previous Week: <strong>$69,524</strong></div>
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
            <th>View</th>
            <th>Revenue</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>The Future of AI</td>
            <td>$29.99</td>
            <td>150</td>
            <td>$4,498.50</td>
          </tr>
          <tr>
            <td>Learn JavaScript</td>
            <td>$24.99</td>
            <td>200</td>
            <td>$4,998.00</td>
          </tr>
          <tr>
            <td>Modern Web Design</td>
            <td>$34.99</td>
            <td>100</td>
            <td>$3,499.00</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets: [
          {
            label: 'Current Week',
            data: [21000,22000,25000,23000,24000,26000,28000],
            borderColor: 'rgba(0,123,255,1)', backgroundColor: 'rgba(0,123,255,0.2)',
            fill: true, tension: 0.4
          },
          {
            label: 'Previous Week',
            data: [22000,23000,25000,24000,26000,28000,30000],
            borderColor: 'rgba(40,167,69,1)', backgroundColor: 'rgba(40,167,69,0.2)',
            fill: true, tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
      }
    });
  </script>
</body>
</html>

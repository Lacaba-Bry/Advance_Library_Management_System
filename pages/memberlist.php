<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Member List | Admin Panel</title>
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
  padding: 20px 20px 10px;
  flex-wrap: wrap;
  gap: 10px;
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
  border: 1px solid var(--border-clr);
  border-radius: 6px;
  font-size: 14px;
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

    .badge.active {
      background-color: #e6f4ea;
      color: var(--success);
    }

    .badge.inactive {
      background-color: #fdecea;
      color: var(--danger);
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
  </style>
</head>
<body>
<main>
    <header class="header">
    <span class="logo-section">
      <span class="logo">Home</span>
    </span>

   
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
<div class="card">
      <div class="summary-cards">
  <div class="card">
    <h3>Total Members</h3>
    <p>4</p>
  </div>
  <div class="card">
    <h3>Last 7 Days</h3>
    <p>2</p>
  </div>
  <div class="card">
    <h3>Last 30 Days</h3>
    <p>3</p>
  </div>
  <div class="card">
    <h3>Club (Monthly)</h3>
    <p>1</p>
  </div>
  <div class="card">
    <h3>Club (Yearly)</h3>
    <p>1</p>
  </div>
  <div class="card">
    <h3>VIP</h3>
    <p>1</p>
  </div>
</div>
</div>



<div class="header-row">

  <h1>Member List</h1>
  <div class="right-controls">
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search by name or email...">
    </div>
    <div class="sort-dropdown">
      <select id="membershipSort">
        <option value="all">Sort by Membership</option>
        <option value="explorer">Explorer (Free)</option>
        <option value="club-monthly">Club (Monthly)</option>
        <option value="club-yearly">Club (Yearly)</option>
        <option value="vip">VIP</option>
      </select>
    </div>
  </div>
</div>

<div class="table-container">
  <table id="memberTable">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Membership</th>
        <th>Date_Created</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>John Santos</td>
        <td>john@example.com</td>
        <td>CLUB (Monthly)</td>
        <td>02/20/2024</td>
        <td><span class="badge active">Active</span></td>
      </tr>
      <tr>
        <td>2</td>
        <td>Mary Cruz</td>
        <td>marycruz@email.com</td>
        <td>VIP</td>
         <td>02/20/2024</td>
        <td><span class="badge active">Active</span></td>
      </tr>
      <tr>
        <td>3</td>
        <td>Alan Reyes</td>
        <td>areyes@mail.com</td>
        <td>Explorer (Free)</td>
         <td>02/20/2024</td>
        <td><span class="badge inactive">Inactive</span></td>
      </tr>
      <tr>
        <td>4</td>
        <td>Grace Lim</td>
        <td>grace.lim@web.com</td>
        <td>CLUB (Yearly)</td>
         <td>02/20/2024</td>
        <td><span class="badge active">Active</span></td>
      </tr>
    </tbody>
  </table>
</div>
</main>
<script>
  const searchInput = document.getElementById('searchInput');
  const sortSelect = document.getElementById('membershipSort');
  const tableRows = document.querySelectorAll('#memberTable tbody tr');

  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedMembership = sortSelect.value;

    tableRows.forEach(row => {
      const rowText = row.innerText.toLowerCase();
      const membershipText = row.cells[3].innerText.toLowerCase();

      const matchesSearch = rowText.includes(searchTerm);
      const matchesMembership =
        selectedMembership === 'all' ||
        (selectedMembership === 'explorer' && membershipText.includes('explorer')) ||
        (selectedMembership === 'club-monthly' && membershipText.includes('club') && membershipText.includes('monthly')) ||
        (selectedMembership === 'club-yearly' && membershipText.includes('club') && membershipText.includes('yearly')) ||
        (selectedMembership === 'vip' && membershipText.includes('vip'));

      row.style.display = matchesSearch && matchesMembership ? '' : 'none';
    });
  }

  searchInput.addEventListener('input', filterTable);
  sortSelect.addEventListener('change', filterTable);
</script>


</body>
</html>

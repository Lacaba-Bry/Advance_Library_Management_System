<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/xx.css">
</head>
<body>
<header class="header">
        <div class="logo-section">
            <div class="logo">LOGO</div>
            <button class="sidebar-toggle">&#9776;</button>
        </div>
        
        <div class="dashboard-title">
            <h1>Dashboard</h1>
        </div>

        <div class="search-bar">
            <input type="text" placeholder="Search ...">
        </div>

        <div class="user-info">
            <div class="notifications">
                <span class="notification-icon">&#128276;</span> <!-- Bell icon -->
                <span class="notification-count">12</span>
            </div>
            <div class="user-profile">
                <img src="profile-pic.jpg" alt="User profile picture" class="profile-img">
                <span class="user-name">Arafat Hossain</span>
                <span class="user-role">Librarian</span>
            </div>
        </div>
    </header>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Users</a></li>
                <li><a href="#">Books</a></li>
                <li><a href="#">Statistics</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="greeting">
                    <h1>Hello, Arafat!</h1>
                    <p>Jan 12, 2023 | Thursday, 11:00 AM</p>
                </div>
                <div class="search">
                    <input type="text" placeholder="Search...">
                </div>
            </div>

            <!-- Stats Section -->
            <div class="stats">
                <div class="card">
                    <h3>Total Visitors</h3>
                    <p>1223</p>
                </div>
                <div class="card">
                    <h3>Borrowed Books</h3>
                    <p>740</p>
                </div>
                <div class="card">
                    <h3>Overdue Books</h3>
                    <p>22</p>
                </div>
                <div class="card">
                    <h3>New Members</h3>
                    <p>60</p>
                </div>
            </div>

            <!-- Books List -->
            <div class="books-list">
                <h2>Books List</h2>
                <table>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Available</th>
                    </tr>
                    <tr>
                        <td>1001</td>
                        <td>Ancestors Trouble</td>
                        <td>Maud Newton</td>
                        <td>30</td>
                    </tr>
                    <tr>
                        <td>1002</td>
                        <td>Life is Everywhere</td>
                        <td>Lucy Lyons</td>
                        <td>27</td>
                    </tr>
                    <tr>
                        <td>1003</td>
                        <td>Stalker</td>
                        <td>Amando Patkin</td>
                        <td>15</td>
                    </tr>
                </table>
            </div>

            <!-- Overdue Book List -->
            <div class="overdue-books">
                <h2>Overdue Book List</h2>
                <table>
                    <tr>
                        <th>User ID</th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Overdue Days</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>1001</td>
                        <td>800-0001</td>
                        <td>Ancestors Trouble</td>
                        <td>5 days</td>
                        <td>Returned</td>
                    </tr>
                    <tr>
                        <td>1002</td>
                        <td>800-0002</td>
                        <td>Life is Everywhere</td>
                        <td>3 days</td>
                        <td>Returned</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js script for displaying statistics
        var ctx = document.getElementById('visitorChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Visitors',
                    data: [120, 150, 200, 180, 210],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
require_once __DIR__ . '/../backend/config/config.php'; // Database connection

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details from the session
$accountId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email'] ?? 'Unknown';
$userType = $_SESSION['user_type'] ?? 'Free'; // Default to Free plan if user_type is not set

// Fetch user's full name and Plan_Name from the database
$stmt = $conn->prepare("
    SELECT r.Fullname, p.Plan_Name
    FROM register r
    JOIN accountlist a ON r.Register_ID = a.Register_ID
    JOIN plans p ON a.Plan_ID = p.Plan_ID
    WHERE a.Account_ID = ?
");
$stmt->bind_param("i", $accountId);
$stmt->execute();
$stmt->bind_result($userName, $planName);
$stmt->fetch();
$stmt->close();

// Set the user plan type from the result
$userType = $planName ?? 'Free';  // If no plan is found, default to Free

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

// Debugging: Output avatar path (optional, remove in production)
error_log("Avatar Path: " . $avatar);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Document</title>
    <style>
        /* Header Styling */
        .top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: white;
            border-bottom: 1px solid #ddd;
            font-family: sans-serif;
        }

        .left-section, .right-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .right-section {
            margin-right: 52px;
        }

        .center-section {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .logo {
            width: 50px;
            height: auto;
        }


.nav-links .dropdown {
  position: relative;
  display: inline-block;
}

.dropbtn {
  display: flex;
  align-items: center;
  background: none;
  border: none;
  font-size: 14px;
  cursor: pointer;
  padding: 5px 10px;
  font-weight: 500;
  gap: 4px;
  transition: background 0.3s ease;
}

.material-icons.dropdown-icon {
  font-size: 20px;
  vertical-align: middle;
}

.dropdown-content {
  display: none;
  position: absolute;
  top: 100%;
  background: white;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  z-index: 10;
  border-radius: 6px;
  min-width: 130px;
  transition: all 0.3s ease;
}

.dropdown:hover .dropdown-content {
  display: block;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.dropdown-content a {
  padding: 12px 16px;
  display: block;
  text-decoration: none;
  color: #333;
  font-size: 14px;
  border-radius: 4px;
  transition: background 0.3s ease;
}

.dropdown-content a:hover {
  background-color: #f1f1f1;
}

        .premium-btn {
            background-color: #f1e8ff;
            color: #4b0082;
            border: none;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Profile Avatar */
        .profile-avatar {
            width: 40px;  /* Set avatar width */
            height: 40px; /* Set avatar height */
            border-radius: 50%; /* Circular shape */
            object-fit: cover; /* Ensures the image covers the area */
            border: 2px solid #ddd; /* Optional border around the avatar */
            cursor: pointer;
        }

        .profile {
            position: relative;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-type {
            font-size: 14px;
            color: #555;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 10;
            border-radius: 6px;
            min-width: 130px;
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-content {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-content a {
            padding: 12px 16px;
            display: block;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }


            .search-bar {
            width: 300px;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            }
        .btn {
            background-color: crimson;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: darkred;
        }

        .search-result {
    padding: 10px;
    margin: 5px 0;
    border-bottom: 1px solid #ddd;
}

.search-result h4 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.search-result p {
    font-size: 14px;
    color: #666;
}

.search-result a {
    display: inline-block;
    margin-top: 5px;
    padding: 6px 12px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

.search-result a:hover {
    background-color: #0056b3;
}
#search-results {
    max-height: 300px;
    overflow-y: auto;  /* Enables scrolling if results exceed max height */
    border: 1px solid #ccc;
    background-color: #f9f9f9;
    padding: 10px;
    border-radius: 5px;
}

    </style>
</head>
<body>
  <header class="top-nav">
    <div class="left-section">
        <img src="Logo.jpg" class="logo" alt="Logo" />
        <nav class="nav-links">
          <div class="dropdown">
            <button class="dropbtn">
              Browse
              <span class="material-icons dropdown-icon">arrow_drop_down</span>
            </button>
            <div class="dropdown-content">
              <a href="home.php">Home</a>
              <a href="genres.php">Genres</a>
            </div>
          </div>
        </nav>
    </div>

    <div class="center-section">
    <input type="text" class="search-bar" id="search-input" placeholder="Search" />
    <div id="search-results"></div>
</div>

    <div class="right-section">
        <button class="premium-btn">⚡ Upgrade Plan</button>
        <span class="user-type"><?php echo htmlspecialchars($userType); ?> Plan</span>
        <div class="profile dropdown">
            <div class="user-info">
                <!-- Display User Avatar -->
                <img src="<?php echo htmlspecialchars($avatar); ?>" class="profile-avatar" alt="User Avatar" />
            </div>
            <div class="dropdown-content">
                <a href="profile.php">Profile</a>
                <a href="backend/logout.php">Logout</a>
            </div>
        </div>
        <span class="user-type"><?php echo htmlspecialchars($userName); ?></span>
    </div>
  </header>



</body>
</html>
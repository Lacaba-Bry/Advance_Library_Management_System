<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
     
    <title>Document</title>
</head>
<style>



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
  gap: 4px; /* spacing between text and icon */
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
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  z-index: 10;
}

.dropdown:hover .dropdown-content {
  display: block;
}






.search-bar {
  width: 300px;
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
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

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
}

.profile {
  position: relative;
}
.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.user-type {
  font-size: 14px;
  color: #555;
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


</style>
<body>
    <body>
  <!-- Header from home.php -->
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
            <a href="#">Home</a>
            <a href="#">Genres</a>
          </div>
        </div>
      </nav>
    </div>

    <div class="center-section">
      <input type="text" class="search-bar" placeholder="Search" />
    </div>

    <div class="right-section">
      <button class="premium-btn">⚡ Try Premium</button>

      <div class="profile dropdown">
        <div class="user-info">
          <img src="sample1.jpg" class="avatar" alt="User" />
        </div>
        <div class="dropdown-content">
          <a href="#">Profile</a>
          <a href="#">Logout</a>
        </div>
      </div>
      <span class="user-type">Free User</span>
    </div>
  </header>
</body>

</body>
</html>
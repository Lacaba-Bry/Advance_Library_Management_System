<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="css/admin.css?v=1.2">
  <script type="text/javascript" src="app.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  
  <nav id="sidebar">
  <ul>
    <li class="logo-section">
      <img src="Logo.jpg" class="LOGOS" alt="Logo" />
      <span class="logo">Admin Panel</span>
      <button onclick="toggleSidebar()" id="toggle-btn">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
          <path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/>
        </svg>
      </button>

      </li>
    <li class="<?= ($_GET['page'] ?? '') == 'adminhome' ? 'active' : '' ?>">
        <a href="?page=adminhome">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M240-200h120v-200q0-17 11.5-28.5T400-440h160q17 0 28.5 11.5T600-400v200h120v-360L480-740 240-560v360Zm-80 0v-360q0-19 8.5-36t23.5-28l240-180q21-16 48-16t48 16l240 180q15 11 23.5 28t8.5 36v360q0 33-23.5 56.5T720-120H560q-17 0-28.5-11.5T520-160v-200h-80v200q0 17-11.5 28.5T400-120H240q-33 0-56.5-23.5T160-200Zm320-270Z"/></svg>
          <span>Home</span>
        </a>
      </li>
    <li class="<?= ($_GET['page'] ?? '') == 'dashboard' ? 'active' : '' ?>">
        <a href="?page=dashboard">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M520-640v-160q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v160q0 17-11.5 28.5T800-600H560q-17 0-28.5-11.5T520-640ZM120-480v-320q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v320q0 17-11.5 28.5T400-440H160q-17 0-28.5-11.5T120-480Zm400 320v-320q0-17 11.5-28.5T560-520h240q17 0 28.5 11.5T840-480v320q0 17-11.5 28.5T800-120H560q-17 0-28.5-11.5T520-160Zm-400 0v-160q0-17 11.5-28.5T160-360h240q17 0 28.5 11.5T440-320v160q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Z"/></svg>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
       <button onclick="toggleSubMenu(this)" class="dropdown-btn">
  <!-- Member Icon -->
  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#e8eaed">
    <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
  </svg>
  
  <span>Member</span>

  <!-- Down Arrow Icon -->
  <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
    <path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/>
  </svg>
</button>
        <ul class="sub-menu">
          <div>
            <li><a href="?page=memberlist">Member List</a></li>
            <li><a href="?page=banlist">Ban List</a></li>
          </div>
        </ul>
      </li>
      <li>
        <button onclick=toggleSubMenu(this) class="dropdown-btn">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><g data-name="7-Book"><path d="M16 41h28v2H16zM5 42H3V6a6.006 6.006 0 0 1 6-6h35a1 1 0 0 1 1 1v36h-2V2H9a4 4 0 0 0-4 4z"/><path d="M44 48H9a6 6 0 0 1 0-12h35a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM9 38a4 4 0 0 0 0 8h34v-8z"/><path d="M9 1h2v36H9zM39 12h-2V8H17v4h-2V7a1 1 0 0 1 1-1h22a1 1 0 0 1 1 1zM38 20H16a1 1 0 0 1-1-1v-4h2v3h20v-3h2v4a1 1 0 0 1-1 1z"/><path d="M15 14h24v2H15zM15 23h2v2h-2zM19 23h2v2h-2zM27 23h12v2H27zM12 41h2v2h-2z"/></g></svg>
          <span>Books</span>
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
        </button>
        <ul class="sub-menu">
          <div>
            <li><a href="?page=booklist">Book List</a></li>
            <li><a href="?page=bookfeatures">Features Book</a></li>
            <li><a href="#">VIP Book</a></li>
            <li><a href="?page=paidbook">Paid Book</a></li>
          </div>
        </ul>
      </li>
      <li>
        <a href="?page=transaction">
       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
  <g data-name="Transaction">
    <path d="M36 14l6 5-6 5v-3H24v-4h12z"/>
    <path d="M12 34l-6-5 6-5v3h12v4H12z"/>
    <path d="M4 4h40a1 1 0 0 1 1 1v38a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1zm1 2v36h38V6z"/>
  </g>
</svg>

  <span>Transaction</span>
        </a>
      </li>
      <li>
        <a href="?page=logs">
       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M8.38 11.65a1 1 0 0 0 .76.35 1 1 0 0 0 .77-.42l2.85-4a1 1 0 0 0-1.62-1.16l-2.12 3L8.76 9a1 1 0 1 0-1.52 1.3zM8.38 17.65a1 1 0 0 0 .76.35 1 1 0 0 0 .77-.42l2.85-4a1 1 0 0 0-1.62-1.16l-2.12 3-.26-.42a1 1 0 1 0-1.52 1.3zM10.19 19.62a3.19 3.19 0 1 0 3.19 3.19 3.19 3.19 0 0 0-3.19-3.19zm0 4.38a1.19 1.19 0 1 1 0-2.38 1.19 1.19 0 1 1 0 2.38zM24 8.08h-8a1 1 0 0 0 0 2h8a1 1 0 0 0 0-2zM24 15h-8a1 1 0 0 0 0 2h8a1 1 0 0 0 0-2zM24 21.92h-8a1 1 0 0 0 0 2h8a1 1 0 0 0 0-2z"/><path d="M26.15 2H5.85A3.86 3.86 0 0 0 2 5.85v20.3A3.86 3.86 0 0 0 5.85 30h20.3A3.86 3.86 0 0 0 30 26.15V5.85A3.86 3.86 0 0 0 26.15 2zM28 26.15A1.85 1.85 0 0 1 26.15 28H5.85A1.85 1.85 0 0 1 4 26.15V5.85A1.85 1.85 0 0 1 5.85 4h20.3A1.85 1.85 0 0 1 28 5.85z"/></svg>
          <span>Logs</span>
        </a>
      </li>
      <li>
        <a href="backend/logout.php">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><g data-name="check out"><path d="M27.9 2.58a.86.86 0 0 0-.07-.1.71.71 0 0 0-.19-.23h-.09a1.12 1.12 0 0 0-.25-.11L27.1 2H12a1 1 0 0 0-1 1v6a1 1 0 0 0 2 0V4h7.19l-3.48 1A1 1 0 0 0 16 6v19h-3v-3a1 1 0 0 0-2 0v4a1 1 0 0 0 1 1h4v2a1 1 0 0 0 .4.8 1 1 0 0 0 .6.2 1 1 0 0 0 .29 0l10-3a1 1 0 0 0 .71-1V3a1 1 0 0 0-.1-.42zM26 25.26l-8 2.4V6.74l8-2.4z"/><path d="M7.41 17H14a1 1 0 0 0 0-2H7.41l1.3-1.29a1 1 0 0 0-1.42-1.42l-3 3a1 1 0 0 0-.21.33 1 1 0 0 0 0 .76 1 1 0 0 0 .21.33l3 3a1 1 0 0 0 1.42 0 1 1 0 0 0 0-1.42zM20 17a1 1 0 0 0 0-2 1 1 0 1 0 0 2z"/></g></svg>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main content area -->
 <?php
  $page = $_GET['page'] ?? 'adminhome';
  $allowed_pages = ['dashboard', 'adminhome','memberlist','banlist','booklist','paidbook','bookfeatures','transaction','logs'];

  if (in_array($page, $allowed_pages)) {
      $filepath = __DIR__ . "/pages/{$page}.php";
      if (file_exists($filepath)) {
          echo "<!-- Including $filepath -->";
          include $filepath;
      } else {
          echo "<h2>Error: Page '{$page}.php' not found.</h2>";
      }
  } else {
      echo "<h2>Invalid page.</h2>";
  }
?>

</body>
</html>
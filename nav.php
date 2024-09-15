<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="scripts.js" defer></script>
    <style>
    nav {
        background-color: #444;
        padding: 1em; 
    }

    nav ul {
        list-style-type: none; /* Remove bullet points */
        margin: 0;
        padding: 0;
        display: flex; 
        justify-content: space-around; 
    }

    nav ul li {
        margin: 0;
    }

    nav ul li a {
        text-decoration: none; /* Remove underline from links */
        color: white; 
        padding: 10px 20px;
        display: block;
        font-size: 16px;
        font-family: Arial, sans-serif; 
        transition: background-color 0.3s ease; 
    }

    nav ul li a:hover {
        background-color: #575757;
        border-radius: 5px; 
    }

    .theme-checkbox {
    --toggle-size: 8px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    width: 6.25em;
    height: 3.125em;
    background: -webkit-gradient(linear, left top, right top, color-stop(50%, #efefef), color-stop(50%, #2a2a2a)) no-repeat;
    background: -o-linear-gradient(left, #efefef 50%, #2a2a2a 50%) no-repeat;
    background: linear-gradient(to right, #efefef 50%, #2a2a2a 50%) no-repeat;
    background-size: 205%;
    background-position: 0;
    -webkit-transition: 0.4s;
    -o-transition: 0.4s;
    transition: 0.4s;
    border-radius: 99em;
    position: fixed;
    left: 10px;
    top: 20px;
    gap: 10px;
    display: flex;
    cursor: pointer;
    border: 0;
    font-size: var(--toggle-size);
  }
  
  .theme-checkbox::before {
    content: "";
    width: 2.25em;
    height: 2.25em;
    position: absolute;
    top: 0.438em;
    left: 0.438em;
    background: -webkit-gradient(linear, left top, right top, color-stop(50%, #efefef), color-stop(50%, #2a2a2a)) no-repeat;
    background: -o-linear-gradient(left, #efefef 50%, #2a2a2a 50%) no-repeat;
    background: linear-gradient(to right, #efefef 50%, #2a2a2a 50%) no-repeat;
    background-size: 205%;
    background-position: 100%;
    border-radius: 50%;
    -webkit-transition: 0.4s;
    -o-transition: 0.4s;
    transition: 0.4s;
  }
  
  .theme-checkbox:checked::before {
    left: calc(100% - 2.25em - 0.438em);
    background-position: 0;
  }
  
  .theme-checkbox:checked {
    background-position: 100%;
  }
</style>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">
<nav>
    <ul>
        <input type="checkbox" class="theme-checkbox" onclick="toggleTheme()" <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'checked' : ''; ?>>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="tasks.php">Tasks</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="export.php">Export</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
</body>
</html>
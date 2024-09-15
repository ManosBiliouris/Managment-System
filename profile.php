<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "task_management";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $surname = $row['surname'];
    $email = $row['email'];
} else {
    echo "No user found";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure the body takes up the full height of the viewport */
        }

        nav {
            background-color: #333;
            padding: 1em;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000; /* Ensure the nav is on top of other content */
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
        }

        nav ul li {
            margin: 0;
        }

        nav ul li a {
            text-decoration: none;
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

        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            position: absolute;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center; 
            box-sizing: border-box; 
        }

        h1 {
            font-size: 26px;
            color: black;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
            text-align: center;
        }

        .line {
            border-top: 1px solid #ddd;
            margin: 20px 0;
            width: 100%; 
        }

        .delete-heading {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        .delete-info {
            font-size: 14px;
            color: #666;
            text-align: center;
            margin: 10px 0;
        }

        .delete-info strong {
            font-weight: bold;
            color: black; /* Red color for warning text */
        }

        a.delete-profile {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff4c4c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        a.delete-profile:hover {
            background-color: #ff1f1f;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 15px;
                top: 80px; /* Adjust based on the height of your nav */
            }
        }

        body.dark-mode {
            background-color: #333;
            color: white;
        }

        body.dark-mode header {
            background: #333;
            color: white;
        }

        body.dark-mode #info {
            background-color: #444; /* Slightly lighter for the content */
            color: white;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.1); /* Adjusted for better visibility */
        }

        body.dark-mode h1, 
        body.dark-mode p, 
        body.dark-mode .delete-heading, 
        body.dark-mode .delete-info, 
        body.dark-mode a.delete-profile {
            color: white; /* Ensure all text turns white */
        }

        body.dark-mode a.delete-profile {
            background-color: #ff6c6c; /* Lighter red for visibility on dark background */
        }

        body.dark-mode a.delete-profile:hover {
            background-color: #ff1f1f;
        }

        body.dark-mode nav ul li a {
            color: white; /* Ensure nav links are white */
        }

        body.dark-mode nav ul li a:hover {
            background-color: #575757;
        }  
    </style>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">

<!-- Navigation snippet starts here -->
<?php include 'nav.php';?>
<!-- Navigation snippet ends here -->

<header>
<div class="container" id="info">
    <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
    <p><strong>Name:</strong> <?php echo $name; ?></p>
    <p><strong>Surname:</strong> <?php echo $surname; ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    
    <!-- Line under Email -->
    <div class="line"></div>
    
    <!-- Delete Account Section -->
    <div class="delete-heading">Delete My Account</div>
    <div class="delete-info">
        Permanently delete your account and tasks. <strong>WARNING:</strong> This action cannot be undone!
    </div>
    
    <!-- Delete Profile Button -->
    <a href="delete_profile.php" class="delete-profile">Delete Profile</a>
</div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const accordions = document.querySelectorAll('.accordion-button');
    const themeToggleButton = document.querySelector('.theme'); // Assuming the button has a class 'theme'
    
    // Handle accordions
    accordions.forEach(button => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            content.style.display = content.style.display === 'block' ? 'none' : 'block';
        });
    });

    // Check the saved theme in cookies and apply it
    const savedTheme = getCookie('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeToggleButton.textContent = 'Switch to Light Mode'; // Update button label
    } else {
        themeToggleButton.textContent = 'Switch to Dark Mode';
    }
    });

    // Function to toggle the theme and store the choice in a cookie
    function toggleTheme() {
        const themeToggleButton = document.querySelector('.theme');
        document.body.classList.toggle('dark-mode');
        
        // Update the cookie based on the current theme
        const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        document.cookie = `theme=${theme};path=/;expires=${getCookieExpirationDate()}`;

        // Update the button label based on the theme
        themeToggleButton.textContent = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
    }

    // Function to get a cookie value by its name
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    // Function to set the cookie expiration date (1 year)
    function getCookieExpirationDate() {
        const date = new Date();
        date.setFullYear(date.getFullYear() + 1); // Set the cookie to expire in 1 year
        return date.toUTCString();
    }

</script>
</body>
</html>
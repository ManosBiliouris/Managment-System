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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM users WHERE username='$username'";
    if ($conn->query($sql) === TRUE) {
        session_destroy();
        header("Location: index.html");
        exit();
    } else {
        echo "Error deleting profile: " . $conn->error;
    }
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM tasks WHERE username='$username'";
    if ($conn->query($sql) === TRUE) {
        session_destroy();
        header("Location: index.html");
        exit();
    } else {
        echo "Error deleting profile: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Profile</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js">defer</script>
    <style>
        body {
            border: 0;
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            background-color: rgba(250, 250, 250);
            color: black; 
        }
    </style>
</head>
<body>

<input type="checkbox" class="theme-checkbox" onclick="toggleTheme()"> 

<div class="container">
    <h2>Delete Profile</h2>
    <p>Are you sure you want to delete your profile? This action cannot be undone.</p>

    <form action="delete_profile.php" method="POST">
    <input type="submit" value="Delete Profile">
</form>
</div>
</body>
</html>

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
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$sql = "SELECT simplepush FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $simplepush = $row['simplepush'];

    // Logic for sending notification via Simplepush or other service

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
    <title>Notify</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Navigation snippet starts here -->
<nav>
    <ul>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="tasks.php">Tasks</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="export.php">Export</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<!-- Navigation snippet ends here -->

<h2>Notification</h2>
<p>Notification logic and status display go here.</p>

</body>
</html>

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM users WHERE username='$username'";
    if ($conn->query($sql) === TRUE) {
        session_destroy();
        header("Location: login.html");
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

<h2>Delete Profile</h2>
<p>Are you sure you want to delete your profile? This action cannot be undone.</p>

<form action="delete_profile.php" method="POST">
    <input type="submit" value="Delete Profile">
</form>

</body>
</html>

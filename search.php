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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = $_POST['search'];
    $status = $_POST['status'];
    $username = $_SESSION['username'];

    $sql = "SELECT * FROM tasks WHERE username='$username' AND task LIKE '%$search%' AND status='$status' ORDER BY created_at DESC";
    $result = $conn->query($sql);

    echo "<h2>Search Results</h2>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Task: " . $row['task'] . " - Status: " . $row['status'] . "<br>";
        }
    } else {
        echo "No tasks found";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
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

<h2>Search Tasks</h2>
<form action="search.php" method="POST">
    <label for="search">Search Task:</label>
    <input type="text" id="search" name="search" required><br>

    <label for="status">Status:</label>
    <select id="status" name="status">
        <option value="Pending">Pending</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
    </select><br>

    <input type="submit" value="Search">
</form>

</body>
</html>

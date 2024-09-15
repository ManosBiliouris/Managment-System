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

header('Content-Type: text/xml');
header('Content-Disposition: attachment; filename="tasks.xml"');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
echo "<tasks>";

$sql = "SELECT * FROM tasks WHERE username='{$_SESSION['username']}'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<task>";
        echo "<description>" . htmlspecialchars($row['task']) . "</description>";
        echo "<status>" . $row['status'] . "</status>";
        echo "</task>";
    }
}

echo "</tasks>";

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export</title>
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

<h2>Exporting Your Tasks</h2>
<p>Your tasks have been exported as an XML file. Check your downloads.</p>

</body>
</html>

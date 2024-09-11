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
    $task = $_POST['task'];
    $status = "Pending";

    $sql = "INSERT INTO tasks (username, task, status) VALUES ('$username', '$task', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "Task added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM tasks WHERE username='$username' ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
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

<h2>Your Tasks</h2>
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Task: " . $row['task'] . " - Status: " . $row['status'] . "<br>";
    }
} else {
    echo "No tasks found";
}
?>

<form action="tasks.php" method="POST">
    <label for="task">New Task:</label>
    <input type="text" id="task" name="task" required><br>
    <input type="submit" value="Add Task">
</form>

</body>
</html>

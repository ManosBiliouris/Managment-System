<?php
session_start();
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

// Check if task is passed via POST
if (isset($_POST['task'])) {
    $task = $_POST['task'];
    $user = $_SESSION['username']; // Get the username from the session

    // Check if task already exists for the user
    $sql = "SELECT * FROM tasks WHERE username = ? AND task = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $task);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'duplicate';  // Task already exists
    } else {
        echo 'available';  // Task does not exist
    }
    $stmt->close();
}

$conn->close();
?>

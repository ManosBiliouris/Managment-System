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

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
     // Verify the password
     if (password_verify($password, $row['password'])) {
        // Password is correct, set the session
        $_SESSION['username'] = $username;

        // Redirect to the profile page after login
        header("Location: profile.php");
        exit();
    } else {
        // Invalid password
        echo "Invalid password";
    }
} else {
    // No user found
    echo "No user found";
}

// Close the database connection
$conn->close();
?>
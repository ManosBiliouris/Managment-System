<?php
session_start();

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

// Get the username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

// Use prepared statements to prevent SQL injection
$sql = $conn->prepare("SELECT * FROM users WHERE username = ?");
$sql->bind_param("s", $username);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Verify the password
    if (password_verify($password, $row['password'])) {
        // Set session variable
        $_SESSION['username'] = $username;
        header("Location: profile.php");
        exit();  // Make sure the script stops after redirecting
    } else {
        echo "Invalid password";
    }
} else {
    echo "No user found";
}

$sql->close();
$conn->close();
?>

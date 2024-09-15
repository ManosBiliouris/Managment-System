<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Database connection settings
$servername = "localhost";
$dbusername = "root";  // Database username
$dbpassword = "";      // Database password
$dbname = "task_management";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's username from the session
$username = $_SESSION['username'];

// Query to get the Simplepush key for the user
$sql = "SELECT simplepush FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the user's Simplepush key
    $row = $result->fetch_assoc();
    $simplepush = $row['simplepush'];

    // Check if the Simplepush key exists
    if (!empty($simplepush)) {
        // Prepare the notification details
        $title = "GEIA";
        $message = "XA DOULEPSE";

        // Simplepush API URL
        $url = "https://api.simplepush.io/send";

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Setup the POST data to send to Simplepush API
        $post_data = http_build_query([
            'key' => $simplepush,  // The Simplepush key for the user
            'title' => $title,     // Notification title
            'msg' => $message      // Notification message
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // Execute the request
        $response = curl_exec($ch);

        // Check if there was an error with the request
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            // Get the HTTP response code
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_status == 200) {
                echo "Notification sent successfully!";
            } else {
                echo "Failed to send notification. HTTP Status Code: " . $http_status;
                echo "Response: " . $response;
            }
        }

        // Close the cURL session
        curl_close($ch);
    } else {
        // Simplepush key is missing
        echo "No Simplepush key found for this user.";
    }
} else {
    // No user found in the database
    echo "No user found with the username: " . $username;
}

// Close the database connection
$conn->close();
?>

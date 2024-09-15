<?php
// Database connection (replace with your actual connection details)
$conn = new mysqli('localhost', 'root', '', 'task_management');

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if task_id and new_status are set
if (isset($_POST['task_id']) && isset($_POST['new_status'])) {
    $taskId = $_POST['task_id'];
    $newStatus = $_POST['new_status'];

    // Prepare the SQL query to update the task status
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $newStatus, $taskId);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        // Redirect back to the tasks page (or wherever you'd like)
        header("Location: tasks.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>

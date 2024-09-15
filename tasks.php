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

$user = $_SESSION['username'];

// Fetch Simplepush key for the logged-in user
$sql = "SELECT simplepush FROM users WHERE username='$user'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$simplepush_key = $row['simplepush'];

// Handle task addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && !isset($_POST['edit_task_id'])) {
    $task = $_POST['task'];
    $status = $_POST['status'];

    $sql = "INSERT INTO tasks (username, task, status) VALUES ('$user', '$task', '$status')";

    if ($conn->query($sql) === TRUE) {

        // Send Simplepush notification
        $title = "New task added";
        $message = $task; // The task name
        $url = "https://api.simplepush.io/send";

        $data = array(
            'key' => $simplepush_key, // User's Simplepush key
            'title' => $title,
            'msg' => $message
        );

        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch); // Execute the request

        curl_close($ch); // Close the cURL session
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle status editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task_id']) && isset($_POST['new_status'])) {
    $task_id = $_POST['edit_task_id'];
    $new_status = $_POST['new_status'];

    $sql = "UPDATE tasks SET status='$new_status' WHERE id='$task_id' AND username='$user'";
}

// Handle task deletion
if (isset($_GET['delete_task_id'])) {
    $task_id = $_GET['delete_task_id'];

    $sql = "DELETE FROM tasks WHERE id='$task_id' AND username='$user'";

    if ($conn->query($sql) === TRUE) {
        echo "Task deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch tasks
$sql = "SELECT * FROM tasks WHERE username='$user' ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="scripts.js" defer></script>
    <title>Tasks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: hidden;
        }

        nav {
            background-color: #333;
            padding: 1em;
            width: 100%;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
        }

        nav ul li {
            margin: 0;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            display: block;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #575757;
            border-radius: 5px;
        }

        .container {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            padding: 20px;
            height: calc(100vh - 370px);
        }

        .new-task-container {
            width: 300px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-right: 40px;
        }

        .task-done {
            text-decoration: line-through;
            letter-spacing: 2px;
            color: #999;
        }

        form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
        }

        input[type="text"],
        select {
            padding: 10px;
            font-size: 16px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #575757;
        }

        /* Tasks container */
        .tasks-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            max-width: 70%;
            height: calc(100vh - 150px);
            overflow-y: auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 24px;
            color: #333;
            text-align: left;
            margin-bottom: 20px;
        }

        .task-list {
            flex-grow: 1;
            overflow-y: auto;
        }

        .task-list div {
            background-color: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .edit-link,
        .delete-link {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
            font-size: 14px;
        }

        .edit-link:hover {
            text-decoration: underline;
        }

        .delete-link {
            color: #ff4c4c;
        }

        .delete-link:hover {
            text-decoration: underline;
        }

        /* Hidden form for editing the task */
        #edit-status-form {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        #edit-status-form label {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .line {
            border-top: 1px solid #ddd;
            margin: 10px 0;
            width: 100%; 
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }
            .new-task-container {
                width: 90%;
                margin-bottom: 20px;
            }
            .tasks-container {
                width: 90%;
            }
        }

        body.dark-mode {
            background-color: #333;
            color: white;
        }

        body.dark-mode nav {
            background-color: #444;
        }

        body.dark-mode .new-task-container,
        body.dark-mode .tasks-container {
            background-color: #444;
            color: white;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .new-task-container input[type="text"],
        body.dark-mode .new-task-container select,
        body.dark-mode .tasks-container .task-list div {
            background-color: #555;
            color: white; /* Text color for input and dropdown options in dark mode */
            border: 1px solid #666;
        }

        body.dark-mode .new-task-container input[type="text"] {
            color: white; /* Ensure text input is white in dark mode */
        }

        body.dark-mode .new-task-container select {
            color: white; /* Ensure dropdown options text is white in dark mode */
        }

        body.dark-mode .tasks-container select {
            color: white; /* Ensure dropdown options text is white in dark mode */
        }

        body.dark-mode .edit-link {
            color: #66aaff;
        }

        body.dark-mode .delete-link {
            color: #ff6b6b;
        }

        body.dark-mode .edit-link:hover {
            text-decoration: underline;
        }

        body.dark-mode .delete-link:hover {
            text-decoration: underline;
        }

        /* Ensure task-done text is white in dark mode */
        body.dark-mode .task-done {
            color: #bbb; /* Adjust color to ensure it's visible on dark background */
        }

        body.dark-mode #edit-status-form {
            border-top: 1px solid #666;
        }

        body.dark-mode label {
            color: #ddd;
        }

        body.dark-mode input[type="submit"] {
            background-color: black;
        }

        body.dark-mode input[type="submit"]:hover {
            background-color: #3b3b3b;
        }

        /* Ensure h2 text color is updated in dark mode */
        body.dark-mode h2 {
            color: #ddd;
        }
    </style>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">

<!-- Navigation snippet starts here -->
<?php include 'nav.php';?>
<!-- Navigation snippet ends here -->

<header>
<div class="container">
    <!-- New Task Box on the left -->
    <div class="new-task-container" id="new-task-box">
        <form action="tasks.php" method="POST">
            <label for="task">New Task:</label>
            <input type="text" id="task" name="task" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Done">Done</option>
            </select>

            <input type="submit" value="Add Task">
        </form>

        <!-- Hidden Edit Task Status Form -->
        <form id="edit-status-form" action="update_task.php" method="POST">
            <input type="hidden" id="edit_task_id" name="task_id">
            
            <label id="update-status-label" for="new_status">Update Status:</label>
            <select id="new_status" name="new_status" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Done">Done</option>
            </select>

            <input type="submit" value="Update Status">
        </form>
    </div>

    <!-- Task List in the middle, scrollable -->
    <div class="tasks-container">
        <h2>Your Tasks</h2>
        <div class="line"></div>
        <div class="task-list" id="list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $taskId = $row['id'];
                    $taskName = htmlspecialchars($row['task']);
                    $taskStatus = htmlspecialchars($row['status']);
                    $createdAt = date('Y-m-d H:i:s', strtotime($row['created_at']));

                    // Check if the task is "Done" to apply the line-through class
                    $class = $taskStatus === 'Done' ? 'task-done' : '';

                    echo "<div>";
                    echo "<strong class=\"$class\">$taskName</strong> - Status: $taskStatus";
                    echo "<br><small>Added on: $createdAt</small>";
                    echo " <a href=\"#\" class=\"edit-link\" onclick=\"editStatus($taskId, '$taskStatus', '$taskName')\">Edit</a>";
                    echo " <a href=\"tasks.php?delete_task_id=$taskId\" class=\"delete-link\" onclick=\"return confirm('Are you sure you want to delete this task?');\">Delete</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No tasks found</p>";
            }
            ?>
        </div>
    </div>
</div>
</header>
<script>
function editStatus(id, currentStatus, taskName) {
    // Show the edit status form
    document.getElementById('edit-status-form').style.display = 'block';

    // Set the task ID in the hidden input
    document.getElementById('edit_task_id').value = id;

    // Set the current status in the dropdown
    document.getElementById('new_status').value = currentStatus;

    // Update the label with the task name
    document.getElementById('update-status-label').textContent = 'Update Status of "' + taskName + '":';

    document.getElementById('edit-status-form').addEventListener('submit', function (event) {
    var taskId = document.getElementById('edit_task_id').value;
    var newStatus = document.getElementById('new_status').value;

    // Update the task appearance in real-time if it's set to Done
    if (newStatus === 'Done') {
        var taskElement = document.querySelector('[data-task-id="' + taskId + '"]');
        taskElement.classList.add('task-done');
        }
    });

}
</script>
</body>
</html>
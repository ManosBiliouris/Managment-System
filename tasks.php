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

    // Check if task already exists
    $sql_check = "SELECT * FROM tasks WHERE username='$user' AND task='$task'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Task already exists, set session error flag
        $_SESSION['task_exists_error'] = true;
        $_SESSION['submitted_task'] = $task; // Store the submitted task value
        header("Location: tasks.php");  // Redirect to the task page to show the error
        exit();
    } else {
        // Insert new task
        $sql = "INSERT INTO tasks (username, task, status) VALUES ('$user', '$task', '$status')";
        if ($conn->query($sql) === TRUE) {
            // Clear error flag after successful task addition
            unset($_SESSION['task_exists_error']);
            unset($_SESSION['submitted_task']); // Clear the submitted task value
            header("Location: tasks.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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

        /* Assigned container*/
        .assigned-container {
            display: flex;
            flex-direction: column;
            width: 70vh;
            height: calc(100vh - 150px);
            overflow-y: auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-left: 40px;
        }
        
        h3 {
            font-size: 24px;
            color: #333;
            text-align: left;
            margin-bottom: 20px;
        }

        .assigned-list {
            flex-grow: 1;
            overflow-y: auto;
        }

        .assigned-list div {
            background-color: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Tasks container */
        .tasks-container {
            display: flex;
            flex-direction: column;
            width: 35%;
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

        input.error {
            border-color: red;
            background-color: #f8d7da;
            color: #721c24;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        body.dark-mode {
            background-color: #333;
            color: white;
        }

        body.dark-mode nav {
            background-color: #444;
        }

        body.dark-mode .new-task-container,
        body.dark-mode .tasks-container,
        body.dark-mode .assigned-container {
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

        body.dark-mode h2 {
            color: #ddd;
        }
        
        body.dark-mode h3 {
            color: #ddd;
        }

        body.dark-mode input.error {
            border-color: darkred;
            background-color: #721c24;
            color: white;
        }

        body.dark-mode .error-message {
            color: darkred;
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
        <form id="new-task-form" action="tasks.php" method="POST">
            <label for="task">New Task:</label>
            <input type="text" id="task" name="task" required>

            <label for="who">Assign to:</label>
            <input type="text" id="who" name="who">
            
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Done">Done</option>
            </select>

            <input type="submit" value="Add Task">
            <div id="task-error" style="color: red; display: none;">Task already exists!</div>
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

    <!-- Task List in the middle -->
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

                    echo "<div data-task-id=\"$taskId\">";
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
    <!-- Assigned List in the right -->
    <div class="assigned">
        <div class="assigned-container">
            <h3>Assigned Tasks</h3>
            <div class="line"></div>
            <div class="assigned-list" id="list">
                
            </div>
        </div>
    </div>
</div>
</header>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const taskInput = document.getElementById('task');
        const errorMessage = document.getElementById('duplicate-error');

        // Check if PHP has set the session error flag
        <?php if (isset($_SESSION['task_exists_error']) && $_SESSION['task_exists_error']) { ?>
            // Show error styling and message
            taskInput.classList.add('error');
            errorMessage.style.display = 'block';  // Show the error message
        <?php } ?>

        taskInput.addEventListener('input', function () {
            // Remove error class and hide message when user starts typing again
            taskInput.classList.remove('error');
            errorMessage.style.display = 'none';
        });
    });

    document.getElementById('new-task-form').addEventListener('submit', function (event) {
        event.preventDefault();  // Prevent form from submitting immediately
        var task = document.getElementById('task').value;

        // AJAX request to check for duplicate task names
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_task.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (this.responseText === 'duplicate') {
                // Show error message
                document.getElementById('task-error').style.display = 'block';
                document.getElementById('task').classList.add('error');
            } else {
                // Submit the form if no duplicates are found
                document.getElementById('new-task-form').submit();
            }
        };
        xhr.send('task=' + encodeURIComponent(task));
    });

    function editStatus(id, currentStatus, taskName) {
        // Show the edit status form
        document.getElementById('edit-status-form').style.display = 'block';

        // Set the task ID in the hidden input
        document.getElementById('edit_task_id').value = id;

        // Set the current status in the dropdown
        document.getElementById('new_status').value = currentStatus;

        // Update the label with the task name
        document.getElementById('update-status-label').textContent = 'Update Status of "' + taskName + '":';
    }
</script>
</body>
</html>

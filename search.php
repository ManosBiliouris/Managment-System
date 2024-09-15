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

$searchResults = ''; // Initialize an empty variable to store search results

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = $_POST['search'];
    $status = $_POST['status'];
    $username = $_SESSION['username'];

    // Construct the SQL query based on whether the status is set or not
    if (empty($status)) {
        $sql = "SELECT * FROM tasks WHERE username='$username' AND task LIKE '%$search%' ORDER BY created_at DESC";
    } else {
        $sql = "SELECT * FROM tasks WHERE username='$username' AND task LIKE '%$search%' AND status='$status' ORDER BY created_at DESC";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $task = htmlspecialchars($row['task']);
            $status = htmlspecialchars($row['status']);
            $created_at = date('Y-m-d H:i:s', strtotime($row['created_at']));

            // Store the output inside the variable
            $searchResults .= "<div><strong>$task</strong> - Status: $status<br><small>Added on: $created_at</small></div>";
        }
    } else {
        // Store 'No tasks found' message if no results
        $searchResults = "<div>No tasks found</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Tasks</title>
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
            height: calc(100vh - 380px);
        }

        /* Search Box on the left side */
        .search-task-container {
            width: 300px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-right: 40px;
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

        /* Search Results container */
        .results-container {
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

        .task-done {
            text-decoration: line-through;
            letter-spacing: 2px;
            color: #999;
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
            .search-task-container {
                width: 90%;
                margin-bottom: 20px;
            }
            .results-container {
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

        body.dark-mode .search-task-container,
        body.dark-mode .results-container {
            background-color: #444;
            color: white;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
        }

        body.dark-mode input[type="text"],
        body.dark-mode select {
            background-color: #555;
            color: white;
            border: 1px solid #777;
        }

        body.dark-mode .task-list div {
            background-color: #555;
            box-shadow: 0px 2px 5px rgba(255, 255, 255, 0.1);
        }

        body.dark-mode h2 {
            color: #ddd;
        }

        body.dark-mode input[type="submit"] {
            background-color: black;
        }

        body.dark-mode input[type="submit"]:hover {
            background-color: #3b3b3b;
        }

    </style>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">

<!-- Navigation snippet starts here -->
<?php include 'nav.php'; ?>
<!-- Navigation snippet ends here -->

<div class="container">
    <!-- Search Task Box on the left -->
    <div class="search-task-container">
        <h2>Search Tasks</h2>
        <form action="search.php" method="POST">
            <label for="search">Search Task:</label>
            <input type="text" id="search" name="search" placeholder="Task name">

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="">-- Any Status --</option>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Done">Done</option>
            </select>

            <input type="submit" value="Search">
        </form>
    </div>

    <!-- Search Results List in the middle, scrollable -->
    <div class="results-container">
        <h2>Search Results</h2>
        <div class="line"></div>
        <div class="task-list">
            <?php
            // Echo the search results stored in the $searchResults variable
            echo $searchResults;
            ?>
        </div>
    </div>
</div>

</body>
</html>

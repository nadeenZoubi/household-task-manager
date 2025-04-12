<?php
mysqli_report(MYSQLI_REPORT_OFF);
$serverName = "localhost";
$dbUsername = "root";
$dbPassword = "";
$databaseName = "hw3_323962225_323087262";

// Create connection
$conn = new mysqli($serverName, $dbUsername, $dbPassword);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$createDbSql = "CREATE DATABASE IF NOT EXISTS $databaseName";
if ($conn->query($createDbSql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($databaseName);

// Create users table
$createUsersTableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if ($conn->query($createUsersTableSql) === TRUE) {
    echo "Table 'users' created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

// Example user entry
$checkUserSql = "SELECT * FROM users WHERE email = 'test@test.com'";
$userResult = $conn->query($checkUserSql);
if ($userResult->num_rows == 0) {
    $insertUserSql = "INSERT INTO users (first_name, last_name, email, password) VALUES ('Test', 'User', 'test@test.com', 'test123')";
    if ($conn->query($insertUserSql) === FALSE) {
        echo "Error adding user: " . $conn->error;
    }
} else {
    echo "User already exists.<br>";
}

// Create tasks table
$createTasksTableSql = "CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    due_date DATE NOT NULL,
    assigned_to VARCHAR(50) NOT NULL,
    status BOOLEAN NOT NULL DEFAULT 0
)";

if ($conn->query($createTasksTableSql) === TRUE) {
    echo "Table 'tasks' created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

// Example tasks entries
$checkTasksSql = "SELECT * FROM tasks WHERE title IN ('java', 'ML', 'AI')";
$tasksResult = $conn->query($checkTasksSql);
if ($tasksResult->num_rows == 0) {
    $insertTaskSql = "INSERT INTO tasks (title, due_date, assigned_to, status) VALUES
        ('java', '2024-08-01', 'ישראל ישראלי', 0),
        ('ML', '2024-06-02', 'חנה כהן', 1),
        ('AI', '2024-07-03', 'דוד לוי', 0)";
    if ($conn->query($insertTaskSql) === FALSE) {
        echo "Error adding tasks: " . $conn->error;
    }
} else {
    echo "Tasks already exist.<br>";
}

// Create missions table
$createMissionsTableSql = "CREATE TABLE IF NOT EXISTS missions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    added_date DATE NOT NULL,
    assigned_to VARCHAR(50) NOT NULL,
    finished BOOLEAN NOT NULL DEFAULT 0,
    taskId INT UNSIGNED,
    FOREIGN KEY (taskId) REFERENCES tasks(id) ON DELETE SET NULL
)";

if ($conn->query($createMissionsTableSql) === TRUE) {
    echo "Table 'missions' created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

$tasksResult = $conn->query("SELECT * FROM tasks");

if ($tasksResult->num_rows > 0) {
    while ($task = $tasksResult->fetch_assoc()) {
        $taskId = $task['id'];
        $assignedUser = $task['assigned_to'];
        $checkMissionSql = "SELECT * FROM missions WHERE taskId = $taskId";
        $missionResult = $conn->query($checkMissionSql);
        if ($missionResult->num_rows == 0) {
            $insertMissionSql = "INSERT INTO missions (title, added_date, assigned_to, finished, taskId) VALUES
                ('homeWork', '2024-07-28', '$assignedUser', 0, $taskId),
                ('toturial', '2024-08-26', '$assignedUser', 1, $taskId),
                ('Lab', '2024-06-12', '$assignedUser', 0, $taskId)";
            if ($conn->query($insertMissionSql) === TRUE) {
                echo "Missions added successfully for the task $taskId<br>";
            } else {
                echo "Error adding missions for the task $taskId: " . $conn->error . "<br>";
            }
        } else {
            echo "Missions already exist for the task $taskId.<br>";
        }
    }
} else {
    echo "No tasks found.<br>";
}
// sql to create household_members table
$sql = "CREATE TABLE IF NOT EXISTS household_members (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    member_email VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE (user_id, member_email)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table household_members created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();



// Redirect to login page after install
header("Location: login.php");
exit();



<?php
// Include the database connection file
require_once 'db.php';

// Get the query parameter
$query = isset($_GET['q']) ? $_GET['q'] : '';

// Prepare the SQL statement
$sql = "SELECT email FROM users WHERE email LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);

// Bind the parameter and execute the statement
$searchTerm = '%' . $query . '%';
$stmt->bind_param('s', $searchTerm);
$stmt->execute();

// Fetch results
$result = $stmt->get_result();
$emails = [];
while ($row = $result->fetch_assoc()) {
    $emails[] = $row['email'];
}

// Return results as JSON
header('Content-Type: application/json');
echo json_encode($emails);

// Close the statement and connection
$stmt->close();
$conn->close();
?>

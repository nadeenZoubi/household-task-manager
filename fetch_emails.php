<?php
require_once 'db.php';

// Check if the 'term' parameter is set
if (isset($_GET['term'])) {
    $term = $_GET['term'] . '%'; // The input from the user, followed by a wildcard
    $loggedInEmail = $_GET['loggedInEmail']; // The logged-in user's email

    // Prepare the SQL query to select emails that start with the user's input
    
    $stmt = $conn->prepare("SELECT email FROM users WHERE email LIKE ?");
    $stmt->bind_param("s", $term);
    
    $stmt->execute();
    $result = $stmt->get_result();

    $emails = [];
    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }

    // Return results as JSON
    header('Content-Type: application/json');
    echo json_encode($emails);
}

$conn->close();
?>

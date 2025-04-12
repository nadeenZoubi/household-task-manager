<?php
require_once 'db.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if email exists
    $response = ['exists' => $stmt->num_rows > 0];
    $stmt->close();
    $conn->close();

    // Return response as JSON
    echo json_encode($response);
}
?>

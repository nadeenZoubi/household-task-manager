<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_user_email = $_POST['new_user_email'];

    // Get the user ID of 'test@test.com'
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    // Insert the new user into the household_members table
    $stmt = $conn->prepare("INSERT INTO household_members (user_id, member_email) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $new_user_email);
    
    if ($stmt->execute()) {
        echo "User added successfully.";
    } else {
        echo "Error adding user: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    
    // Redirect back to index.php
    header('Location: index.php');
    exit();
}
?>

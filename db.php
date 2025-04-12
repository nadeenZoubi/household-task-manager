<?php
mysqli_report(MYSQLI_REPORT_OFF);
$serverName = "localhost";
$dbUsername = "root";
$dbPassword = "";
$databaseName = "hw3_323962225_323087262";

// יצירת חיבור
$conn = new mysqli($serverName, $dbUsername, $dbPassword, $databaseName);

// בדיקת החיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


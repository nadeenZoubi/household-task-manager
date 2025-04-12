<?php
require("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['missionStatus'])) { 
    $missionStatus = (int)$_POST['missionStatus'];
    $missionId = (int)$_POST['missionId'];

    $sql = "UPDATE missions
            SET finished = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $missionStatus, $missionId);

    // ביצוע ההצהרה המוכנה
    if ($stmt->execute()) {
        echo "Mission status updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // סגירת ההצהרה
    $stmt->close();
} else {
    die("No data was sent");
}
?>

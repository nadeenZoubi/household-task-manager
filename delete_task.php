<?php
require_once 'db.php';

if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    // Prepare and execute the deletion query
    $sql = "DELETE FROM missions WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "No task ID provided"]);
}
?>

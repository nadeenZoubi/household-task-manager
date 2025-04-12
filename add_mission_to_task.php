<?php
require_once 'db.php';

// Set the header to return JSON and disable output buffering
header('Content-Type: application/json');
ob_start();

$response = ['status' => 'error', 'message' => 'Unknown error occurred'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $missionTitle = $_POST['missionTitle'] ?? '';
    $assignTo = $_POST['assignTo'] ?? '';
    $taskId = $_POST['taskId'] ?? '';

    if (!empty($missionTitle) && !empty($assignTo) && !empty($taskId)) {
        // Check if the task already exists to prevent duplicates
        $checkSql = "SELECT * FROM missions WHERE title = ? AND assigned_to = ? AND taskId = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("ssi", $missionTitle, $assignTo, $taskId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response['message'] = 'Task already exists.';
        } else {
            // Insert the new task into the missions table
            $sql = "INSERT INTO missions (title, assigned_to, taskId, added_date) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $missionTitle, $assignTo, $taskId);

            if ($stmt->execute()) {
                $newTaskId = $stmt->insert_id;
                $response = [
                    'status' => 'success',
                    'data' => [
                        'id' => $newTaskId,
                        'title' => $missionTitle,
                        'added_date' => date('Y-m-d'),
                        'assigned_to' => $assignTo,
                        'finished' => 0 // Assuming new tasks are not finished
                    ]
                ];
            } else {
                $response['message'] = 'Failed to add the task: ' . $stmt->error;
            }
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid input data.';
    }
}

$conn->close();

// Clear any previous output and return the JSON response
ob_end_clean();
echo json_encode($response);
exit;

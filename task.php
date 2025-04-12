<?php
require_once 'db.php';

// Handle checkbox status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $taskId = $_POST['task_id'] ?? null;
    $finished = $_POST['finished'] ?? null;

    if ($taskId !== null && $finished !== null) {
        $sql = "UPDATE missions SET finished = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $finished, $taskId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update task status: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid task ID or status.']);
    }
    $conn->close();
    exit;
}

// Fetch the task and its related missions
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Fetch task title
    $sql = "SELECT title FROM tasks WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    $task_title = $task['title'];
    $stmt->close();

    // Fetch missions
    $sql = "SELECT * FROM missions WHERE taskId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $missions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch full names for the dropdown
    $sql = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS full_name, u.email 
            FROM users AS u 
            INNER JOIN household_members AS hm 
            ON hm.member_email = u.email";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = ['full_name' => $row['full_name'], 'email' => $row['email']];
    }
    $conn->close();
} else {
    echo "No task provided!";
    exit();
}
?>

<!doctype html>
<html lang="he" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <title>עמוד רשימה פנימי</title>
</head>

<body class="Unify_text">
    <!-- Include the header -->
    <?php include "header.php" ?>

    <div class="container mt-md-5 mt-2 text-center">
        <h1 class="mb-4"><?php echo htmlspecialchars($task_title); ?></h1>

        <table class="table table-striped" id="missions_table" name="missions_table">
            <thead>
                <tr>
                    <th scope="col">כותרת המשימה</th>
                    <th scope="col">תאריך הוספה</th>
                    <th scope="col">משתמש אחראי</th>
                    <th scope="col">בוצע?</th>
                    <th scope="col">אפשרויות</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($missions as $row): ?>
                    <tr id="task-<?php echo htmlspecialchars($row['id']); ?>">
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['added_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['assigned_to']); ?></td>
                        <td>
                            <input id="status" name="status" type="checkbox" class="form-check-input status" data-id="<?= $row['id']; ?>" <?= $row['finished'] ? 'checked' : ''; ?>>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger delete-task" data-id="<?php echo htmlspecialchars($row['id']); ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Display image based on task title -->
        <?php
        $image = '';
        $imageClass = 'img-fluid mb-4';

        switch (strtolower($task_title)) {
            case 'java':
                $image = 'images/java.png';
                break;
            case 'ml':
                $image = 'images/ML.png';
                break;
            case 'ai':
                $image = 'images/AI.png';
                $imageClass .= ' small-img';
                break;
            default:
                $image = 'images/default.png';
        }
        ?>
        <div class="d-flex justify-content-center">
            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($task_title); ?>" class="<?php echo $imageClass; ?>">
        </div>

        <h4 class="text-decoration-underline m-4">הוסף משימה חדשה</h4>
        <div class="d-flex justify-content-center"> 
            <form class="col-8 col-md-4 mb-5" id="addMissionForm" name="addMissionForm" method="POST" action="add_mission_to_task.php">
                <div class="mb-3">
                    <label for="missionTitle" class="form-label">כותרת המשימה:</label>
                    <input type="text" class="form-control" id="missionTitle" name="missionTitle" placeholder="הכנס כותרת משימה" required>
                </div>
                <div class="mb-3">
                    <label for="assignTo" class="form-label">הקצה למשתמש:</label>
                    <select class="form-select" id="assignTo" name="assignTo">
                        <option value="">בחר משתמש</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo htmlspecialchars($user['full_name']); ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" id="taskId" name="taskId" value="<?php echo htmlspecialchars($task_id); ?>">
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mx-2">הוסף</button>
                </div>
            </form>
        </div>

        <div class="d-flex justify-content-start">
            <button type="button" class="btn btn-secondary mx-2 my-3" onclick="window.history.back()">חזור</button>
        </div>
    </div>

    <!-- Include the footer -->
    <?php include "footer.php"?>

    <script>
     $(document).ready(function() {
        var formSubmitting = false;

        // Handle the form submission to add a new mission
        $('#addMissionForm').on('submit', function(event) {
            event.preventDefault();

            if (formSubmitting) {
                console.log('Form is already submitting.');
                return; 
            }

            formSubmitting = true;

            var form = $(this);
            var formData = form.serialize();
            var submitButton = form.find('button[type="submit"]');

            submitButton.prop('disabled', true);

            console.log('Form submission started.');

            $.ajax({
                url: 'add_mission_to_task.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Form submission response:', response);

                    if (response.status === 'success') {
                        var newTask = response.data;

                        var newRow = `
                            <tr id="task-${newTask.id}">
                                <td>${newTask.title}</td>
                                <td>${newTask.added_date}</td>
                                <td>${newTask.assigned_to}</td>
                                <td>
                                    <input id="status" name="status" type="checkbox" class="form-check-input status" data-id="${newTask.id}" ${newTask.finished ? 'checked' : ''}>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger delete-task" data-id="${newTask.id}">Delete</button>
                                </td>
                            </tr>
                        `;

                        $('#missions_table tbody').append(newRow);

                        // Clear the form fields after success
                        $('#missionTitle').val('');
                        $('#assignTo').val('');
                    } else {
                        alert(response.message || 'Failed to add the task. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error adding task:', error);
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    submitButton.prop('disabled', false);
                    formSubmitting = false;
                    console.log('Form submission complete.');
                }
            });
        });

        // Handle the delete task button click
        $('#missions_table').on('click', '.delete-task', function() {
            var taskId = $(this).data('id');
            var row = $('#task-' + taskId);

            console.log('Attempting to delete task with ID:', taskId);

            if (confirm('Are you sure you want to delete this task?')) {
                $.ajax({
                    url: 'delete_task.php',
                    type: 'POST',
                    data: { task_id: taskId },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Delete response:', response);

                        if (response.status === 'success') {
                            row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert(response.message || 'Failed to delete the task. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting task:', error);
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });

        // Handle the checkbox status change
        $('#missions_table').on('change', '.status', function() {
            var taskId = $(this).data('id');
            var isChecked = $(this).is(':checked') ? 1 : 0;

            console.log('Changing status for task ID:', taskId, 'to:', isChecked);

            $.ajax({
                url: 'task.php', // Sending the request to the same task.php
                type: 'POST',
                data: { action: 'update_status', task_id: taskId, finished: isChecked },
                dataType: 'json',
                success: function(response) {
                    if (response.status !== 'success') {
                        alert('Failed to update task status. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating task status:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
    </script>
</body>
</html>

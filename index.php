<?php
session_start();
require_once 'db.php';

$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Initially hide the table
$show_table = false;

// Check if the logged-in user is `test@test.com`
if ($user_email === 'test@test.com') {
    $show_table = true;
} else {
    // Check if the user has been added by `test@test.com`
    $stmt = $conn->prepare("SELECT * FROM household_members WHERE member_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $show_table = true;
    }

    $stmt->close();
}
?>

<!doctype html>
<html lang="he" dir="rtl">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- jQuery UI CSS & JS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
    <title>עמוד ראשי</title>
    <style>
        .small-img {
            max-width: 50%; /* Adjust the size as needed */
            height: auto;
        }
        .center-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .ui-autocomplete {
            z-index: 1050; /* Ensure it appears above other elements */
        }
        .modal {
            z-index: 1040; /* Ensure modals are below the autocomplete */
        }
        .modal-backdrop {
            z-index: 1030; /* Ensure modal backdrop is below autocomplete */
        }
    </style>
</head>

<body class="Unify_text">

    <!-- Include the header -->
    <?php include "header.php" ?>

    <div class="container text-center my-5">
        <h1 class="fw-bolder text-decoration-underline">רשימת משימות</h1>

        <?php if ($show_table): ?>
        <div class="d-flex justify-content-center my-3">
            <div class="col-md-8">
                <table class="table my-0">
                    <thead>
                        <tr class="fs-5">
                            <th scope="col">מספר</th>
                            <th scope="col">כותרת</th>
                            <th scope="col">תאריך יעד</th>
                            <th scope="col">הוקצה למשתמש</th>
                            <th scope="col">סטטוס</th>
                        </tr>
                    </thead>
                    <tbody class="fs-6">
                        <?php 
                        $sql = "SELECT * FROM tasks";
                        $result = $conn->query($sql);
                        ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <th scope="row"><?= $row['id']; ?></th>
                                <td>
                                    <a href="task.php?id=<?= $row['id']; ?>">
                                        <?= $row['title']; ?>
                                    </a>
                                </td>
                                <td><?= $row['due_date']; ?></td>
                                <td><?= $row['assigned_to']; ?></td>
                                <?php $status = ($row['status'] == 1) ? 'פתוח' : 'סגור'; ?>
                                <td class="<?= $status ?>"><?= $status ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
            <p>אין לך הרשאה לצפות בטבלת המשימות.</p>
        <?php endif; ?>

        <div class="center-content my-4">
            <!-- Button to trigger modal -->
            <?php if ($user_email === 'test@test.com'): ?>
                <button type="button" class="btn btn-primary my-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    הוסף משתמש
                </button>
            <?php endif; ?>

            <!-- Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="fw-bold modal-title" id="exampleModalLabel">הוסף אימייל משתמש</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form to add user email -->
                            <form id="addUserForm" method="post" action="add_user.php">
                                <div class="mb-3">
                                    <label for="userEmail" class="form-label">מייל של המשתמש שברצונכם להוסיף:</label>
                                    <input type="email" class="form-control" id="userEmail" name="new_user_email" required>
                                </div>
                                <button type="submit" class="btn btn-primary">הוסף משתמש</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <img src="images/tasks.webp" class="img-fluid small-img my-4" alt="Tasks Image">
        </div>
    </div>

    <!-- Include the footer -->
    <?php include "footer.php"?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Autocomplete script -->
    <script>
        $(document).ready(function() {
            // Initialize autocomplete
            $("#userEmail").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "fetch_emails.php",
                        type: "GET",
                        dataType: "json",
                        data: {
                            term: request.term // Pass the user input to the server
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching emails:", status, error);
                            response([]);
                        }
                    });
                },
                minLength: 1
            });
        });
    </script>

</body>
</html>

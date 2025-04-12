<?php
require_once 'db.php';
session_start();

// Check if already logged in
if (isset($_SESSION['logged'])) {
    $warning = "You are already logged in, do you want to log out?";
    echo "
    <div class='fw-bold m-0 text-center alert alert-danger' role='alert'>
        $warning
    </div>";
    include 'logout.php';
    exit();
}

if (isset($_POST['Signup'])) {
    $firstName = $_POST['Fname'];
    $lastName = $_POST['Lname'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $confirmPassword = $_POST['Conf_password'];

    // Check if email already exists
    $checkEmailSql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email exists
        echo '<div class="m-0 alert alert-danger text-center" role="alert">
            <strong>המייל כבר קיים במערכת</strong>
        </div>';
        $stmt->close();
    } else if ($password === $confirmPassword) {
        // Passwords match
        // Do not hash the password
        $insertSql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

        if ($stmt->execute()) {
            // Success
            $_SESSION['signup'] = "User successfully registered!";
            header('Location: register.php'); 
            $stmt->close();
        } else {
            // Error during insertion
            echo '<div class="alert alert-danger" role="alert" style="text-align: center;">
                <strong>Error: ' . htmlspecialchars($stmt->error) . '</strong>
            </div>';
        }
        $stmt->close();
    } else {
        // Passwords do not match
        echo '<div class="alert alert-danger" role="alert" style="text-align: center;">
            <strong>הסיסמאות אינן תואמות</strong>
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמה</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <!-- Include the header -->
    <?php include "header.php"; ?>

    <main class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-6 mb-5"> 
                <h2 class="text-center mb-4">הרשמה</h2>
                <?php
                if (isset($_SESSION['signup'])) {
                    echo '<div class="m-0 alert alert-success text-center" role="alert">
                        <strong>' . $_SESSION['signup'] . '</strong>
                    </div>';
                    unset($_SESSION['signup']); // Clear the message after displaying it
                }
                ?>
                <form id="register" action="register.php" method="POST">
                    <div class="mb-3">
                        <label for="Fname" class="form-label">שם פרטי:</label>
                        <input type="text" class="form-control" id="Fname" name="Fname" required>
                    </div>
                    <div class="mb-3">
                        <label for="Lname" class="form-label">שם משפחה:</label>
                        <input type="text" class="form-control" id="Lname" name="Lname" required>
                    </div>
                    <div class="mb-3">
                        <label for="Email" class="form-label">דוא"ל:</label>
                        <input type="email" class="form-control" id="Email" name="Email" required>
                        <div id="email-feedback"></div> <!-- Feedback message will be shown here -->
                    </div>
                    <div class="mb-3">
                        <label for="Password" class="form-label">סיסמא:</label>
                        <input type="password" class="form-control" id="Password" name="Password" required>
                    </div>
                    <div class="mb-3">
                        <label for="Conf_password" class="form-label">אישור סיסמא:</label>
                        <input type="password" class="form-control" id="Conf_password" name="Conf_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="Signup" name="Signup">הרשמה</button>
                </form>
            </div>
            <div class="col-md-6 col-6">
                <video class="img-fluid" autoplay muted loop>
                    <source src="images/Todo List.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </main>

    <!-- Include the footer -->
    <?php include "footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function () {
        // Function to check if email exists
        function checkEmailExists(email) {
            return $.ajax({
                url: 'check_email.php',
                type: 'GET',
                data: { email: email },
                dataType: 'json'
            });
        }

        // Bind keyup event to the email input field
        $('#Email').on('keyup', function () {
            let email = $(this).val();

            if (email.length > 0) {
                checkEmailExists(email).done(function (response) {
                    if (response.exists) {
                        $('#email-feedback').text('This email is already registered.').css('color', 'red');
                        $('#Signup').attr('disabled', true); // Disable submit button
                    } else {
                        $('#email-feedback').text('Email is available.').css('color', 'green');
                        $('#Signup').attr('disabled', false); // Enable submit button
                    }
                });
            } else {
                $('#email-feedback').text('');
                $('#Signup').attr('disabled', false); // Enable submit button if email input is empty
            }
        });
    });
    </script>
</body>
</html>

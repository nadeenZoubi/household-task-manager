<?php
require_once 'db.php';

session_start();

// Display logout success message
if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message'];
    echo "
        <div class='fw-bold m-0 text-center alert alert-success' role='alert'>
            $logoutMessage
        </div>";
    // Remove the logout message from session after displaying it
    unset($_SESSION['logout_message']);
}

// Check for signup success
if (isset($_SESSION['signup'])) {
    $success = "הרשמה הושלמה בהצלחה! עכשיו תוכל להתחבר עם המייל והסיסמא שלך.";
    echo "
        <div class='fw-bold m-0 text-center alert alert-success' role='alert'>
            $success
        </div>";
    // Remove signup session after displaying message
    unset($_SESSION['signup']);
}

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

// Handle login
else if (isset($_POST['login']) && isset($_POST['Email']) && isset($_POST['Password'])) {
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $exists = $stmt->get_result();

    if ($exists->num_rows > 0) {
        // Set session variables upon successful login
        $_SESSION['logged'] = true;
        $_SESSION['email'] = $email;

        // Handle "remember me" functionality
        if (isset($_POST['remember-me'])) {
            $cookie_value = base64_encode($email); // Security Encoding 
            $expiry_time = time() + (14 * 24 * 60 * 60); // Set the expiration time to 14 days
            setcookie('remember', $cookie_value, $expiry_time, "/"); // Set cookie path to root
        }

        header('Location: index.php');
        exit();
    } 
    // User not found
    else {  
        $error = "אימייל או סיסמא שגויים";
        echo "
        <div class='fw-bold m-0 text-center alert alert-danger' role='alert'>
            $error
        </div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <title>התחברות למערכת</title>
</head>
<body>
    <!-- Include the header -->
    <?php include "header.php"; ?>

    <main class="container main-container">
        <div class="row">
            <div class="col-6">
                <img src="images/tasks.webp" class="img-fluid">
            </div>
            <div class="col-6">
                <div class="login-form">
                    <h2 class="text-center">התחברות למערכת</h2>
                    <form id="login" action="login.php" method="POST">
                        <div class="form-group">
                            <label for="Email">דוא"ל:</label>
                            <input type="email" class="form-control" id="Email" name="Email" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
                        </div>
                        <div class="form-group">
                            <label for="Password">סיסמה:</label>
                            <input type="password" class="form-control" id="Password" name="Password" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember-me" name="remember-me">
                            <label class="form-check-label" for="remember-me">זכור אותי</label>
                        </div>
                        <button type="submit" class="btn btn-primary" name="login">התחבר</button>
                        <div class="register-link text-center">
                            <a href="register.php">הרשמה לאתר</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Include the footer -->
    <?php include "footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.rtl.min.js"></script>
</body>
</html>

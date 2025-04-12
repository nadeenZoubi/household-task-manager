<?php
session_start();

// Handle logout
if (isset($_POST['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Unset the remember me cookie if it exists
    if (isset($_COOKIE['remember'])) {
        unset($_COOKIE['remember']);
        setcookie('remember', '', time() - 3600, '/'); // Empty value and old timestamp
    }

    // Set a session variable to indicate logout success
    session_start();
    $_SESSION['logout_message'] = "התנתקת בהצלחה מהמערכת";

    // Redirect to login page
    header('Location: login.php');
    exit();
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
    
    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
    <title>עמוד התנתקות</title>
</head>

<body class="Unify_text">
    <!-- Include the header -->
    <?php include "header.php"; ?>

    <main class="container main-container d-flex justify-content-center align-items-start mt-5">
        <div class="logout-form">
            <h2 class="text-center">התנתקות מהמערכת</h2>
            <form id="logout" action="logout.php" method="POST">
                <button id="logout" name="logout" class="btn btn-primary" type="submit">התנתק</button>
            </form>
        </div>
    </main>

    <!-- Include the footer -->
    <?php include "footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.rtl.min.js"></script>
</body>
</html>

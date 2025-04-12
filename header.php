<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['logged']) && isset($_POST['signup'])) {
    $warning = "You are already logged in, do you want to log out??";
    echo "
    <div class='fw-bold m-0 text-center alert alert-danger' role='alert'>
        $warning
    </div>";
} else if (isset($_POST['signup'])) {
    header('Location: register.php');
    exit();
}
?>

<header class="header-container">
    <div>
        <a href="login.php" class="nav-button">התחברות</a>
        <a href="logout.php" class="nav-button">יציאה</a>
    </div>
    <div class="nav-container">
        <a href="register.php" class="mx-2">הרשמה</a>
        <a href="index.php" class="mx-2">ניהול מטלות</a>
    </div>
    <div>
        <img src="images/TODO.png" alt="TODO Logo" class="header-logo">
    </div>
</header>


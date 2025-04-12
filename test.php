<?php
session_start();

if (!isset($_SESSION['email'])) {
    $_SESSION['email'] = 'test@test.com';
}

echo "Session email: " . htmlspecialchars($_SESSION['email']);
?>

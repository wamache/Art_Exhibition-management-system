<?php
session_start(); // Start the session

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page or home page after logging out
header("Location: /project/art_exhibition/admin/login.php");
exit;
?>

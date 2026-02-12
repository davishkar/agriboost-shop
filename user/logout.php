<?php
/**
 * User Logout
 * AGRIBOOST SHOP - User Session Termination
 */

// Start session
session_start();

// Destroy user session
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);

// Destroy the session completely
session_destroy();

// Redirect to user login page
header('Location: login.php');
exit();
?>

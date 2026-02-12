<?php
/**
 * Admin Logout
 * AGRIBOOST SHOP - Admin Session Termination
 */

// Start session
session_start();

// Destroy admin session
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);

// Destroy the session completely
session_destroy();

// Redirect to admin login page
header('Location: login.php');
exit();
?>

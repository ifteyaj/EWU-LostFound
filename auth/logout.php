<?php
/**
 * Logout Handler
 */
require_once '../init.php';

// Log out the user
logoutUser();

// Redirect to login with success message
header("Location: login.php?success=logout_success");
exit();
?>

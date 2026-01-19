<?php
/**
 * Logout Logic
 * Destroys the session and redirects to login page.
 */

session_start();
session_unset();
session_destroy();

header("Location: ../index.php");
exit();
?>

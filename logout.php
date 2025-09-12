<?php
session_start();
session_destroy(); // Destroy the session
header("Location: loginn.php"); // Redirect to the login page
exit();
?>
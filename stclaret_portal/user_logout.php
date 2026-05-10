<?php
session_start();
$_SESSION = [];
session_destroy();

// Redirect to the home page (e.g., index.php)
header('Location: index.php'); 
exit();

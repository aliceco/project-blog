<?php
session_start();
$_SESSION = [];
session_destroy();
// Redirect to the page they came from, or home if none
$redirect = $_SERVER['HTTP_REFERER'] ?? '/project-blog/index.php';
header('Location: ' . $redirect);
exit;

?>
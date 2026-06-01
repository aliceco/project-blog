<?php
session_start();

if (
  $_SERVER['REQUEST_METHOD'] !== 'POST' ||
  empty($_POST['csrf_token']) ||
  empty($_SESSION['csrf_token']) ||
  !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
  http_response_code(403);
  exit('Forbidden');
}

$_SESSION = [];
session_destroy();
header('Location: /project-blog/index.php');
exit;

?>
<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

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
header('Location: ' . BASE_URL . 'index.php');
exit;

?>

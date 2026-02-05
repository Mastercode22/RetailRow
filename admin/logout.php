<?php
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
$result = $auth->logout();

header('Location: login.php');
exit();
?>
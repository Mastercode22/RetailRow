<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$url = 'http://localhost/RetailRow/api/products.php?type=category&category_id=9';
$response = file_get_contents($url);
var_dump($response);
?>
<?php
// Session test
session_start();

if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = time();
    echo "Session started at: " . date('Y-m-d H:i:s', $_SESSION['test']) . "<br>";
} else {
    echo "Session exists from: " . date('Y-m-d H:i:s', $_SESSION['test']) . "<br>";
}

echo "Session ID: " . session_id() . "<br>";
echo "Session save path: " . session_save_path() . "<br>";
echo "Session status: " . session_status() . "<br>";

echo "<br><a href='session_test.php'>Refresh</a>";
echo "<br><a href='admin/login.php'>Go to Login</a>";
?>
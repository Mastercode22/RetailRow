<?php
// Simple login test
require_once __DIR__ . '/config/auth.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = $auth->login($email, $password);

    if ($result['success']) {
        echo "✅ Login successful! Redirecting...<br>";
        echo "<script>setTimeout(() => window.location.href = 'admin/dashboard.php', 2000);</script>";
    } else {
        echo "❌ Login failed: " . $result['message'] . "<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Login Test</title>
</head>
<body>
    <h1>Simple Login Test</h1>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" value="admin@retailrow.com" required><br>
        <input type="password" name="password" placeholder="Password" value="admin123" required><br>
        <button type="submit">Login</button>
    </form>
    <br>
    <a href="admin/login.php">Go to Real Login</a>
</body>
</html>
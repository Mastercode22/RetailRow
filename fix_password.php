<?php
// Fix admin password script
require_once __DIR__ . '/config/db.php';

$database = new Database();
$db = $database->getConnection();

// Generate correct hash for admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Generated hash for 'admin123': $hash<br>";

// Update the admin user password
$query = "UPDATE users SET password = :password WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $hash);
$stmt->bindParam(':email', 'admin@retailrow.com');

if ($stmt->execute()) {
    echo "✅ Admin password updated successfully!<br>";
    echo "You can now login with: admin@retailrow.com / admin123<br>";
} else {
    echo "❌ Failed to update password<br>";
}

// Verify the update
$query = "SELECT password FROM users WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', 'admin@retailrow.com');
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    $verify = password_verify('admin123', $user['password']);
    echo "Password verification test: " . ($verify ? '✅ PASSED' : '❌ FAILED') . "<br>";
}
?>
<?php
// Password hash verification
$password = 'admin123';
$hash_from_db = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "<h1>Password Hash Verification</h1>";
echo "Password: $password<br>";
echo "Hash from DB: $hash_from_db<br>";
echo "Verification: " . (password_verify($password, $hash_from_db) ? '✅ CORRECT' : '❌ INCORRECT') . "<br>";

// Generate new hash
$new_hash = password_hash($password, PASSWORD_DEFAULT);
echo "<br>New hash: $new_hash<br>";
echo "New hash verification: " . (password_verify($password, $new_hash) ? '✅ CORRECT' : '❌ INCORRECT') . "<br>";

// Update database with correct hash
require_once __DIR__ . '/config/db.php';

$database = new Database();
$db = $database->getConnection();

$query = "UPDATE users SET password = :password WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $new_hash);
$stmt->bindParam(':email', 'admin@retailrow.com');

if ($stmt->execute()) {
    echo "<br>✅ Database updated with correct password hash<br>";
    echo "You can now login with: admin@retailrow.com / admin123<br>";
} else {
    echo "<br>❌ Failed to update database<br>";
}
?>
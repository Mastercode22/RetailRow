<?php
// Import database script
require_once __DIR__ . '/config/db.php';

$database = new Database();
$db = $database->getConnection();

echo "<h1>Database Import</h1>";

// Read SQL file
$sql = file_get_contents(__DIR__ . '/db_enhanced.sql');

if (!$sql) {
    echo "❌ Could not read db.sql file<br>";
    exit();
}

// Split into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$errors = 0;

foreach ($statements as $statement) {
    if (empty($statement)) continue;

    try {
        $db->exec($statement);
        $success++;
    } catch (Exception $e) {
        echo "❌ Error executing: " . substr($statement, 0, 50) . "...<br>";
        echo "Error: " . $e->getMessage() . "<br>";
        $errors++;
    }
}

echo "<br>✅ Successfully executed $success statements<br>";
if ($errors > 0) {
    echo "❌ $errors errors occurred<br>";
}

echo "<br><a href='check_db.php'>Check Database Status</a>";
echo "<br><a href='admin/login.php'>Go to Admin Login</a>";
?>
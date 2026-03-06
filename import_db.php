<?php

// Database configurations
require_once __DIR__ . '/config.php';

$host = $config['server'];
$dbname = $config['database_name'];
$user = $config['username'];
$pass = $config['password'];
$port = isset($config['port']) ? $config['port'] : 3306;

$sql_file = __DIR__ . '/migration-docs/it_helpdesk.sql';

if (!file_exists($sql_file)) {
    die("Error: SQL file not found at $sql_file\n");
}

echo "Starting database import...\n";
echo "Host: $host\n";
echo "Database: $dbname\n";
echo "----------------------------------------\n";

try {
    // Connect to MySQL
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database if it doesn't exist
    echo "Creating database if it doesn't exist...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // Read SQL file
    echo "Reading SQL file...\n";
    $sql = file_get_contents($sql_file);

    // Execute SQL statements
    echo "Executing SQL queries (this may take a moment)...\n";

    // We can execute the whole file at once or statement by statement. 
    // Executing the whole file might fail if max_allowed_packet is too small.
    // Given the file has 13k lines, let's execute it directly since PDO can usually handle it 
    // if configured to allow multiple statements (which is default for MySQL in PHP).
    // Or, for safer execution, we can split by queries (';').

    // For a robust approach, we'll execute it directly using prepared statements is not possible, 
    // so we use exec().
    $pdo->exec($sql);

    echo "----------------------------------------\n";
    echo "Success! Database imported successfully.\n";

} catch (PDOException $e) {
    echo "----------------------------------------\n";
    echo "Database Error: " . $e->getMessage() . "\n";
    // Check if it's an auth error or something else
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "Tip: Please double check the username and password in config.php\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
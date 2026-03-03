<?php
/**
 * Database Backup Script
 * Alternative to mysqldump for Windows 11
 */

// Configuration
$host = 'localhost';
$user = 'root';
$pass = '';  // Ganti jika ada password
$dbname = 'it_helpdesk';
$outputFile = 'C:/backup/it_helpdesk_' . date('Ymd_His') . '.sql';

// Create backup directory if not exists
$backupDir = dirname($outputFile);
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Connect to database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database: $dbname\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Start output
$output = "-- Database Backup: $dbname\n";
$output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$output .= "-- Host: $host\n\n";
$output .= "SET FOREIGN_KEY_CHECKS=0;\n";
$output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$output .= "SET time_zone = \"+00:00\";\n\n";

// Get all tables
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Found " . count($tables) . " tables\n\n";

foreach ($tables as $table) {
    echo "Exporting table: $table ... ";
    
    // Drop table if exists
    $output .= "\n-- Table: $table\n";
    $output .= "DROP TABLE IF EXISTS `$table`;\n";
    
    // Get CREATE TABLE statement
    $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $output .= $createTable['Create Table'] . ";\n\n";
    
    // Get table data
    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($rows) > 0) {
        $output .= "-- Data for table: $table\n";
        
        foreach ($rows as $row) {
            $columns = array_keys($row);
            $values = array_values($row);
            
            // Escape values
            $escapedValues = array_map(function($value) use ($pdo) {
                if ($value === null) {
                    return 'NULL';
                }
                return $pdo->quote($value);
            }, $values);
            
            $output .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
        }
        
        $output .= "\n";
        echo count($rows) . " rows\n";
    } else {
        echo "0 rows\n";
    }
}

$output .= "SET FOREIGN_KEY_CHECKS=1;\n";

// Write to file
file_put_contents($outputFile, $output);

echo "\n✅ Backup completed successfully!\n";
echo "File saved: $outputFile\n";
echo "File size: " . round(filesize($outputFile) / 1024 / 1024, 2) . " MB\n";
?>

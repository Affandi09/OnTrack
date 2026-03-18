<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';
try {
    $pdo = new PDO("mysql:host=".$config['server'].";dbname=".$config['database_name'].";charset=".$config['charset'], $config['username'], $config['password']);
    $stmt = $pdo->query("SELECT id, ticket, timestamp, clientid FROM tickets ORDER BY id DESC LIMIT 10");
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total recent tickets: " . count($tickets) . "\n\n";
    foreach ($tickets as $t) {
        echo "ID: " . $t['id'] . " | Ticket: " . $t['ticket'] . " | Date: " . $t['timestamp'] . " | Client: " . $t['clientid'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

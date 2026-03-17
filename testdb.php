<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'config.php';
$pdo = new PDO("mysql:host=".$config['server'].";dbname=".$config['database_name'].";charset=".$config['charset'], $config['username'], $config['password']);
$stmt = $pdo->query("SELECT id, ticket, timestamp, clientid FROM tickets ORDER BY id DESC LIMIT 5");
echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";

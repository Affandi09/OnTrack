<?php
require 'includes/config.php';
require 'includes/classes/class.database.php';
$database = new medoo([
    "database_type" => "mysql",
    "database_name" => $config['database_name'],
    "server" => $config['server'],
    "username" => $config['username'],
    "password" => $config['password'],
    "charset" => $config['charset'],
    "port" => $config['port']
]);
$startdate = "2020-01-01 00:00:00";
$enddate = "2028-01-01 23:59:59";
$tickets = $database->select("tickets", "*", [
    "timestamp[<>]" => [$startdate, $enddate]
]);
echo "Tickets count: " . count($tickets) . "\n";

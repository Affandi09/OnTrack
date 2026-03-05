<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$scriptpath = '/var/www/html';
require('/var/www/html/config.php');
require('/var/www/html/vendor/autoload.php');
require('/var/www/html/vendor/classes/class.medoo.php');

$database = new medoo($config);

$submitter_id_temp = 1;
$prefix = "IT";

$company_id = $database->get("submitters", "company_id", ["id" => $submitter_id_temp]);
if ($company_id) {
    $company_name = $database->get("companies", "name", ["id" => $company_id]);
    if ($company_name) {
        $prefix = strtoupper(str_replace(' ', '', $company_name));
    }
}

echo "Prefix: " . $prefix . "\n";

$stmt = $database->query("SELECT ticket FROM tickets WHERE ticket LIKE '{$prefix}-%' ORDER BY id DESC LIMIT 1");
$last_ticket = $stmt ? $stmt->fetchColumn() : false;

echo "Last Ticket: " . var_export($last_ticket, true) . "\n";

$new_number = 1;
if ($last_ticket) {
    $parts = explode("-", $last_ticket);
    if (isset($parts[1]) && is_numeric($parts[1])) {
        $new_number = intval($parts[1]) + 1;
    }
}

$random = $prefix . "-" . str_pad($new_number, 5, "0", STR_PAD_LEFT);
while ($database->has("tickets", ["ticket" => $random])) {
    $new_number++;
    $random = $prefix . "-" . str_pad($new_number, 5, "0", STR_PAD_LEFT);
}

echo "Generated Ticket: " . $random . "\n";

$ticketid = $database->insert("tickets", [
    "ticket" => $random,
    "departmentid" => 0,
    "branch_id" => 7,
    "submitter_id" => 1,
    "locationid" => 0,
    "kendala" => 'HARDWARE',
    "clientid" => 0,
    "userid" => 1,
    "adminid" => 1,
    "assetid" => 0,
    "projectid" => 0,
    "email" => 'test@example.com',
    "subject" => 'Test Ticket Script',
    "status" => "Open",
    "priority" => "Normal",
    "timestamp" => date('Y-m-d H:i:s'),
    "notes" => "",
    "ccs" => "",
    "timespent" => 0
]);

echo "Inserted Ticket ID: " . $ticketid . "\n";

<?php
require("config.php");
try {
    $pdo = new PDO("mysql:host=" . $config['server'] . ";dbname=" . $config['database_name'] . ";charset=utf8", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = [
        "type" => "user",
        "roleid" => 1,
        "clientid" => 0,
        "name" => "Debug User",
        "email" => "debug" . time() . "@example.com",
        "ldap_user" => 0,
        "title" => "Developer",
        "mobile" => "123456",
        "password" => sha1("password"),
        "theme" => "skin-blue",
        "sidebar" => "opened",
        "layout" => "",
        "notes" => "",
        "signature" => "",
        "sessionid" => "",
        "resetkey" => "",
        "autorefresh" => 0,
        "lang" => "en",
        "ticketsnotification" => 1,
        "avatar" => ""
    ];

    print "Testing insert into people table...\n\n";

    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));

    $sql = "INSERT INTO people ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);

    $result = $stmt->execute(array_values($data));
    if ($result) {
        print "Success! User inserted with ID: " . $pdo->lastInsertId() . "\n";
    }

} catch (PDOException $e) {
    print "Database Error: " . $e->getMessage() . "\n";
}
?>
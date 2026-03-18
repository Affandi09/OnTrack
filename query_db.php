<?php
// Let's use SQLite or MySQL depending on config if we can find it
$dbFile = __DIR__ . "/database.db";
if (file_exists($dbFile)) {
    $pdo = new PDO("sqlite:" . $dbFile);
    $stmt = $pdo->query("SELECT id, ticket, timestamp, clientid FROM tickets ORDER BY id DESC LIMIT 5");
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} else {
    echo "No database.db found";
}

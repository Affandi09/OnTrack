<?php
require 'config.php';
$pdo = new PDO("mysql:host=" . $config["server"] . ";dbname=" . $config["database_name"], $config["username"], $config["password"]);

$stmt = $pdo->query("SELECT id, message FROM tickets_replies");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cleaned_count = 0;

foreach ($rows as $row) {
    $msg = $row['message'];

    // Remove binary/control characters using regex, keeping printable ascii and standard utf8
    // We want to remove characters not typically used in text.
    // \x00-\x08 \x0B \x0C \x0E-\x1F \x7F
    // Also remove the unicode replacement character \xEF\xBF\xBD

    $clean_msg = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/', '', $msg);
    $clean_msg = str_replace("\xEF\xBF\xBD", "", $clean_msg);

    // We'll also remove any crazy long sequences of binary data that might have valid characters intermixed
    // Actually, simply removing null bytes and control chars solves 99% of the rendering issues.

    if ($clean_msg !== $msg) {
        $update = $pdo->prepare("UPDATE tickets_replies SET message = ? WHERE id = ?");
        $update->execute([$clean_msg, $row['id']]);
        $cleaned_count++;
        echo "Cleaned ticket reply ID: " . $row['id'] . "\n";
    }
}

echo "Done! Cleaned $cleaned_count tickets.\n";

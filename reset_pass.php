<?php
// Load configuration
require_once __DIR__ . '/config.php';

$host = $config['server'];
$dbname = $config['database_name'];
$user = $config['username'];
$pass = $config['password'];
$port = isset($config['port']) ? $config['port'] : 3306;

echo "<h3>OnTrack Emergency Password Reset</h3>";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 1. Check if the people table exists and counts users
    $stmt = $pdo->query("SELECT id, email, password, type FROM people LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<b>Total Accounts Found in DB:</b> " . count($users) . "<br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Email</th><th>Type</th><th>Password Hash</th></tr>";

    foreach ($users as $u) {
        $hash_preview = substr($u['password'], 0, 15) . '...';
        echo "<tr><td>{$u['id']}</td><td>{$u['email']}</td><td>{$u['type']}</td><td>{$hash_preview}</td></tr>";
    }
    echo "</table><br>";

    // If there is a POST request, update the first admin's password
    if (isset($_POST['reset_password'])) {
        $new_pass = $_POST['new_password'];
        $email_to_reset = $_POST['email_to_reset'];

        // Assuming password_hash() with BCRYPT or SHA1 is used - check current hashing strategy First. 
        // In older systems it's sha1(). In modern ones it's password_hash(). Let's test sha1 first as fallback or md5, but password_hash is standard.
        // Wait, standard OnTrack might use SHA1.
        // I will use SHA1 if hashes look like 40 chars hex.

        $is_sha1 = (strlen($users[0]['password']) == 40 && ctype_xdigit($users[0]['password']));

        if ($is_sha1) {
            $hashed_pass = sha1($new_pass);
            echo "<i>Hashing method detected: SHA1</i><br>";
        } else {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            echo "<i>Hashing method detected: BCRYPT (password_hash)</i><br>";
        }

        $upd = $pdo->prepare("UPDATE people SET password = ? WHERE email = ?");
        $upd->execute([$hashed_pass, $email_to_reset]);

        echo "<h3 style='color:green'>Success! Password for {$email_to_reset} has been changed to: {$new_pass}</h3>";
        echo "<script>alert('Password updated! Please log in now.');</script>";
    }

    echo "
    <hr>
    <h3>Reset An Account</h3>
    <form method='POST'>
        Email of Account: <input type='email' name='email_to_reset' required><br><br>
        New Password: <input type='text' name='new_password' value='123456' required><br><br>
        <input type='submit' name='reset_password' value='Reset Password Now'>
    </form>
    ";

} catch (Exception $e) {
    echo "<b style='color:red'>Error connecting to database:</b> " . $e->getMessage() . "<br>";
}
?>
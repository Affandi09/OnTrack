<?php
$scriptpath = "/var/www/html";
require $scriptpath . "/config.php";
require $scriptpath . "/includes/loader.php";
$purifier = new HTMLPurifier();
echo "PURIFY START
";
$purifier->purify("<p>test</p>");
echo "
PURIFY DONE
";
?>

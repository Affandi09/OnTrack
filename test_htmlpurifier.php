<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$scriptpath = __DIR__;
require "config.php";
require __DIR__ . "/includes/functions.php";
spl_autoload_register("vendorClassAutoload");
spl_autoload_register("appClassAutoload");
require __DIR__ . "/vendor/autoload.php";

echo "BEFORE PURIFIER\n";
$hpconfig = HTMLPurifier_Config::createDefault();
$hpconfig->set("HTML.AllowedAttributes", "src, height, width, alt");
$hpconfig->set("URI.AllowedSchemes", array("http" => true, "https" => true, "mailto" => true, "ftp" => true, "nntp" => true, "news" => true, "callto" => true, "data" => true));
$purifier = new HTMLPurifier($hpconfig);
echo "INIT PURIFIER\n";
$purifier->purify("<p>test</p>");
echo "AFTER PURIFIER\n";
?>
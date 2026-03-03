<?php
$scriptpath = __DIR__;
require __DIR__ . "/includes/functions.php";
vendorClassAutoload("HTMLPurifier_Node_Text");
appClassAutoload("HTMLPurifier_Node_Text");
echo "DONE AUTOLOAD TEST\n";

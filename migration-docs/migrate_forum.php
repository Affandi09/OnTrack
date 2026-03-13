<?php
if (file_exists(__DIR__ . '/includes/loader.php')) {
  $scriptpath = __DIR__;
} else {
  $scriptpath = dirname(__DIR__);
}
require($scriptpath . '/includes/loader.php');

$sql4 = "CREATE TABLE `satisfaction_surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `q1` int(1) NOT NULL,
  `q2` int(1) NOT NULL,
  `q3` int(1) NOT NULL,
  `q4` int(1) NOT NULL,
  `q5` int(1) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

try {
  $database->query($sql4);
  echo "Table satisfaction_surveys created successfully.\n";
} catch (Exception $e) {
  echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>
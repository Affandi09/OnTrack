<?php
if (file_exists(__DIR__ . '/includes/loader.php')) {
  $scriptpath = __DIR__;
} else {
  $scriptpath = dirname(__DIR__);
}
require($scriptpath . '/includes/loader.php');

$sql1 = "CREATE TABLE IF NOT EXISTS `forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `peopleid` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql2 = "CREATE TABLE IF NOT EXISTS `forum_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `peopleid` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql3 = "ALTER TABLE `files` 
  ADD COLUMN IF NOT EXISTS `forumtopicid` int(11) NOT NULL DEFAULT '0' AFTER `projectid`,
  ADD COLUMN IF NOT EXISTS `forumreplyid` int(11) NOT NULL DEFAULT '0' AFTER `forumtopicid`;";

try {
  $database->query($sql1);
  echo "Table forum_topics created successfully.\n";
  $database->query($sql2);
  echo "Table forum_replies created successfully.\n";

  // MariaDB might not support 'IF NOT EXISTS' for columns directly in all versions, 
  // but applying it safely via ignoring errors if it already exists.
  try {
    $database->query($sql3);
    echo "Table files altered successfully (forumtopicid, forumreplyid added).\n";
  } catch (Exception $e) {
    // If the columns already exist, this might throw an error we can ignore safely for idempotency
    echo "Note: " . $e->getMessage() . " (Columns might already exist)\n";
  }


} catch (Exception $e) {
  echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>
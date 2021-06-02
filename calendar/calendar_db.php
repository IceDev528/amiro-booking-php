<?php
$host = "127.0.0.1";
$port = 3306;
$username = "root";
$password = "";
$database = "Booking";

$db = new PDO("mysql:host=$host;port=$port",
    $username,
    $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// other init
date_default_timezone_set("UTC");
session_start();

$db->exec("CREATE DATABASE IF NOT EXISTS `$database`");
$db->exec("use `$database`");

function tableExists($dbh, $id)
{
  $results = $dbh->query("SHOW TABLES LIKE '$id'");
  if(!$results) {
    return false;
  }
  if($results->rowCount() > 0) {
    return true;
  }
  return false;
}

$exists = tableExists($db, "events");

if (!$exists) {
  $db->exec("CREATE TABLE IF NOT EXISTS events (
                        id INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
                        room TEXT,
                        name TEXT, 
                        start DATETIME NOT NULL, 
                        end DATETIME NOT NULL,
                        color VARCHAR(30)
                        )");

  $stmt = $db->prepare($insert);

  $stmt->bindParam(':room', $room);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':start', $start);
  $stmt->bindParam(':end', $end);
  $stmt->bindParam(':color', $color);

  foreach ($items as $it) {
    $room = $it['room'];
    $name = $it['name'];
    $start = $it['start'];
    $end = $it['end'];
    $color = $it['color'];
    $stmt->execute();
  }

}

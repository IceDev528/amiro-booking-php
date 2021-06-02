<?php
require_once 'calendar_init.php';

// .events.load() passes start and end as query string parameters by default
$start = $_GET["start"];
$end = $_GET["end"];

$stmt = $db->prepare('SELECT * FROM events WHERE room = "Hartford IT Suite" AND NOT ((end <= :start) OR (start >= :end))');

$stmt->bindParam(':start', $start);
$stmt->bindParam(':end', $end);

$stmt->execute();
$result = $stmt->fetchAll();

class Event {}
$events = array();

foreach($result as $row) {
  $e = new Event();
  $e->id = $row['id'];
  $e->room = $row['room'];
  $e->text = $row['name'];
  $e->start = $row['start'];
  $e->end = $row['end'];
  $e->backColor = $row['color'];
  $events[] = $e;
}

header('Content-Type: application/json');
echo json_encode($events);

<?php
require_once 'calendar_init.php';

$json = file_get_contents('php://input');
$params = json_decode($json);

$insert = "INSERT INTO events (room, name, start, end) VALUES (:room, :name, :start, :end)";

$stmt = $db->prepare($insert);

$stmt->bindParam(':room', $params->room);
$stmt->bindParam(':start', $params->start);
$stmt->bindParam(':end', $params->end);
$stmt->bindParam(':name', $params->text);

$stmt->execute();

class Result {}

$response = new Result();
$response->result = 'OK';
$response->id = $db->lastInsertId();
$response->message = 'Created with id: '.$db->lastInsertId();

header('Content-Type: application/json');
echo json_encode($response);

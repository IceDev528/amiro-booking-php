<?php
require_once 'calendar_init.php';

$json = file_get_contents('php://input');
$params = json_decode($json);

$insert = "UPDATE events SET color = :color WHERE id = :id";

$stmt = $db->prepare($insert);

$stmt->bindParam(':color', $params->color);
$stmt->bindParam(':id', $params->id);

$stmt->execute();

class Result {}

$response = new Result();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
echo json_encode($response);

<?php
namespace Roboto;
session_start();

include_once 'vendor/autoload.php';

$savedState = isset($_SESSION['robot_state']) ? $_SESSION['robot_state'] : null;

$robot = new Robot(new RobotState($savedState));

$payload = json_decode(file_get_contents('php://input'));

try{
    $robot->commander($payload->command);
    http_response_code(200);
    echo $robot->report();
}
catch(InvalidMovementException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
catch(InvalidCommandException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
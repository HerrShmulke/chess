<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Cords.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Board.php';

$board = new Board();
$board->newGame();

$response = (object)[];
$response->success = true;
$response->error = '';

header('Content-Type: application/json');
echo json_encode($response);
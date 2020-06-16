<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Cords.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Board.php';

header('Content-Type: application/json');

$figureX = (int)$_GET['figure_x'];
$figureY = (int)$_GET['figure_y'];
$positionX = (int)$_GET['pos_x'];
$positionY = (int)$_GET['pos_y'];

$error = (object)array('success' => false, 'error' => 'Переданы не все параметры');

if (!isset($figureX) || !isset($figureY) || !isset($positionX) || !isset($positionY))
  exit(json_encode($error));

$board = new Board();
$figure = $board->getFigure(new Cords($figureX, $figureY));
$response = $board->moveFigure($figure, new Cords($positionX, $positionY));


echo json_encode($response);

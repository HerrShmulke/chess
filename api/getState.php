<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Cords.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Board.php';

$board = new Board();

header('Content-Type: application/json');
echo json_encode($board);
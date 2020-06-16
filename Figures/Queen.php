<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Figure.php';

class Queen extends Figure
{

  public function __construct($color, $cords)
  {
    $this->collision = true;
    parent::__construct($color, $cords, "Queen");
  }

  /**
   * @inheritDoc
   */
  public function canMove($x, $y)
  {
    if ($this->outField($x, $y)) return false;

    $xx = $this->cords->x;
    $yy = $this->cords->y;

    return $this->cords->x == $x || $this->cords->y == $y || abs($this->cords->x - $x) == abs($this->cords->y - $y);
  }
}
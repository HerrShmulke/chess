<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Figure.php';

class Boat extends Figure
{

  public function __construct($color, $cords)
  {
    parent::__construct($color, $cords, "Boat");
  }

  /**
   * @inheritDoc
   */
  public function canMove($x, $y)
  {
    if ($this->outField($x, $y)) return false;

    return $this->cords->x == $x || $this->cords->y == $y;
  }
}
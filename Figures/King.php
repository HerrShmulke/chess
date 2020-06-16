<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Figure.php';

class King extends Figure
{

  public function __construct($color, $cords)
  {
    $this->collision = true;
    parent::__construct($color, $cords, "King");
  }

  /**
   * @inheritDoc
   */
  public function canMove($x, $y)
  {
    if ($this->outField($x, $y)) return false;

    return abs($this->cords->x - $x) <= 1 && abs($this->cords->y - $y) <= 1;
  }
}
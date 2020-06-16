<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Figure.php';

class Elephant extends Figure
{

  public function __construct($color, $cords)
  {
    parent::__construct($color, $cords, "Elephant");
  }

  /**
   * @inheritDoc
   */
  public function canMove($x, $y)
  {
    if ($this->outField($x, $y)) return false;

    return abs($this->cords->x - $x) == abs($this->cords->y - $y);
  }
}
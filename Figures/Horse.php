<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Figure.php';

class Horse extends Figure
{

  public function __construct($color, $cords)
  {
    $this->collision = false;
    parent::__construct($color, $cords, "Horse");
  }

  /**
   * @inheritDoc
   */
  public function canMove($x, $y)
  {
    if ($this->outField($x, $y)) return false;

    $dx = abs($this->cords->x - $x);
    $dy = abs($this->cords->y - $y);

    return $dx == 1 && $dy == 2 || $dx == 2 && $dy == 1;
  }
}
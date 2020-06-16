<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Figure.php';

class Pawn extends Figure
{
  public $firstStep = true;
  public $direction;

  public function __construct($color, $cords)
  {
    $this->collision = true;
    $this->direction = $color == 'dark' ? 1 : -1;
    parent::__construct($color, $cords, "Pawn");
  }

  /**
   * @inheritDoc
   */
  public function canMove($x, $y)
  {
    if ($this->outField($x, $y)) return false;

    $dx = abs($this->cords->x - $x);

    if ($dx != 0)
      return false;

    return $y == $this->cords->y + $this->direction || (($y == $this->cords->y + ($this->direction * 2)) && $this->firstStep);
  }
}
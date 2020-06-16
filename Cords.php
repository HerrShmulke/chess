<?php

class Cords
{
  public $x;
  public $y;

  public function __construct($x, $y)
  {
    $this->x = $x;
    $this->y = $y;
  }

  public function setCords($x, $y)
  {
    $this->x = $x;
    $this->y = $y;
  }
}
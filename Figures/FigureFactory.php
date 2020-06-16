<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Boat.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Elephant.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Horse.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/King.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Pawn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/Queen.php';

class FigureFactory
{
  /**
   * @param $color string
   * @param $cords Cords
   * @return Boat
   */
  public static function Boat($color, $cords)
  {
    return new Boat($color, $cords);
  }

  /**
   * @param $color string
   * @param $cords Cords
   * @return Elephant
   */
  public static function Elephant($color, $cords)
  {
    return new Elephant($color, $cords);
  }

  /**
   * @param $color string
   * @param $cords Cords
   * @return Horse
   */
  public static function Horse($color, $cords)
  {
    return new Horse($color, $cords);
  }

  /**
   * @param $color string
   * @param $cords Cords
   * @return King
   */
  public static function King($color, $cords)
  {
    return new King($color, $cords);
  }

  /**
   * @param $color string
   * @param $cords Cords
   * @return Pawn
   */
  public static function Pawn($color, $cords)
  {
    return new Pawn($color, $cords);
  }

  /**
   * @param $color string
   * @param $cords Cords
   * @return Queen
   */
  public static function Queen($color, $cords)
  {
    return new Queen($color, $cords);
  }
}
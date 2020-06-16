<?php
  include_once $_SERVER['DOCUMENT_ROOT'] . '/Cords.php';

  abstract class Figure
  {

    /** @var string dark || white */
    public $color;
    /** @var Cords $cords */
    public $cords;
    /** @var boolean $collision */
    public $collision = true;
    /** @var string $name */
    public $name;

    /**
     * Figure constructor.
     * @param $color string
     * @param $cords Cords
     * @param $name string
     * @throws Exception
     */
    public function __construct($color, $cords, $name)
    {
      if ($this->outField($cords->x, $cords->y)) throw new Exception('Выход за пределы поля');

      $this->color = strtolower($color);
      $this->cords = $cords;
      $this->name = $name;
    }

    /**
     * Проверяет, может ли фигура походить на данные координаты
     * true - может
     * false - не может
     * @param $x int
     * @param $y int
     * @return boolean
     */
    abstract public function canMove($x, $y);



    /**
     * Метод проверяет, вышли ли координаты за пределы поля
     * true - вышли
     * false - не вышли
     * @param $x int
     * @param $y int
     * @return bool
     */
    protected function outField($x, $y)
    {
      return $x > 7 || $x < 0 || $y > 7 || $y < 0;
    }
  }
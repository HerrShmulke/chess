<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/Figures/FigureFactory.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Cords.php';

class Board
{
  /** @var string $goingColor */
  public $goingColor = 'white';
  /** @var Figure[][] $boardArray */
  public $boardArray = [];

  public $darkKing;
  public $whiteKing;

  public function __construct()
  {
    if (!$this->load())
    {
      $this->newGame();
    }
  }

  /**
   * @param $figure Figure
   * @param $position Cords
   * @return object
   */
  public function moveFigure($figure, $position)
  {
    if ($this->checkShah())
    {
      if ($this->checkMat())
      {
        return $this->getResponse(false, "Шах и мат! Король игрока $this->goingColor не может ходить");
      }

      $tempBoard = $this->boardArray;
    }

    if ($figure == NULL)
      return $this->getResponse(false, 'Ячейка пустая');

    $response = $this->canMoveFigure($figure, $position);

    if (!$response->success)
      return $response;

    $this->boardArray[$figure->cords->y][$figure->cords->x] = NULL;
    $figure->cords->setCords($position->x, $position->y);
    $this->setFigureOnBoard($figure);

    if (isset($tempBoard) && $this->checkShah())
    {
      $this->boardArray = $tempBoard;
      return $this->getResponse(false, 'Королю поставлен мат');
    }

    /** @var $figure Pawn */
    if ($figure->name == 'Pawn') $figure->firstStep = false;
    $this->goingColor = ($this->goingColor == 'white') ? 'dark' : 'white';

    $this->save();
    return $this->getResponse(true, '');
  }

  /**
   * Метод проверяет, может ли фигура переместиться на заданные координаты
   * true - может
   * false - не может
   * @param $figure Figure
   * @param $position Cords
   * @param bool $checkCourse
   * @return object
   */
  public function canMoveFigure($figure, $position, $checkCourse = true)
  {
    if ($figure->color != $this->goingColor && $checkCourse)
      return $this->getResponse(false, 'Сейчас ходит противоположный игрок');

    if ($position->x > 7 || $position->x < 0 || $position->y > 7 || $position->x < 0)
      return $this->getResponse(false, 'Выход за пределы доски');

    if ($figure->name == 'Pawn')
    {
      if (!$this->coursePawn($figure, $position))
        return $this->getResponse(false, 'Данная фигура не может так ходить');
    }
    else if (!$figure->canMove($position->x, $position->y)) {
      return $this->getResponse(false, 'Данная фигура не может так ходить');
    }

    if ($figure->name == 'King')
    {
      if ($this->checkShahOnCords($this->getCurrentKing(), $position))
        return $this->getResponse(false, 'Королю будет поставлен шах');
    }

    if ($figure->collision && $this->collisionDetect($figure, $position))
      return $this->getResponse(false, 'Фигура не может проходить сквозь другие фигуры');

    $target = $this->getFigure($position);
    if ($target != NULL && $target->color == $figure->color)
      return $this->getResponse(false, 'Ячейка занята');



    return $this->getResponse(true, '');
  }

  /**
   * Метод начинает новую игру
   */
  public function newGame()
  {
    for ($i = 0; $i < 8; ++$i)
      for ($j = 0; $j < 8; ++$j)
        $this->boardArray[$i][$j] = NULL;

    $this->goingColor = 'white';

    $this->setFigureOnBoard(FigureFactory::Boat('dark', new Cords(0, 0)));
    $this->setFigureOnBoard(FigureFactory::Boat('dark', new Cords(7, 0)));

    $this->setFigureOnBoard(FigureFactory::Horse('dark', new Cords(1, 0)));
    $this->setFigureOnBoard(FigureFactory::Horse('dark', new Cords(6, 0)));

    $this->setFigureOnBoard(FigureFactory::Elephant('dark', new Cords(2, 0)));
    $this->setFigureOnBoard(FigureFactory::Elephant('dark', new Cords(5, 0)));

    $this->setFigureOnBoard(FigureFactory::Queen('dark', new Cords(3, 0)));
    $this->setFigureOnBoard(FigureFactory::King('dark', new Cords(4, 0)));


    $this->setFigureOnBoard(FigureFactory::Boat('white', new Cords(0, 7)));
    $this->setFigureOnBoard(FigureFactory::Boat('white', new Cords(7, 7)));

    $this->setFigureOnBoard(FigureFactory::Horse('white', new Cords(1, 7)));
    $this->setFigureOnBoard(FigureFactory::Horse('white', new Cords(6, 7)));

    $this->setFigureOnBoard(FigureFactory::Elephant('white', new Cords(2, 7)));
    $this->setFigureOnBoard(FigureFactory::Elephant('white', new Cords(5, 7)));

    $this->setFigureOnBoard(FigureFactory::Queen('white', new Cords(3, 7)));
    $this->setFigureOnBoard(FigureFactory::King('white', new Cords(4, 7)));

    for ($i = 0; $i < 8; ++$i)
    {
      $this->setFigureOnBoard(FigureFactory::Pawn('dark', new Cords($i, 1)));
      $this->setFigureOnBoard(FigureFactory::Pawn('white', new Cords($i, 6)));
    }

    $this->save();
  }

  /**
   * Метод загружает состояние игры из state.json
   * Вернет true в случае успеха и false в случае неудачи
   * @return boolean
   */
  public function load()
  {
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/state.json')) return false;

    $date = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/state.json'));

    $this->darkKing = $date->darkKing;
    $this->whiteKing = $date->whiteKing;
    $this->goingColor = $date->goingColor;

    for ($i = 0; $i < 8; ++$i)
      for ($j = 0; $j < 8; ++$j)
        $this->boardArray[$i][$j] = NULL;

    /** @var Figure[][] $boardArray */
    $boardArray = $date->boardArray;

    for ($i = 0; $i < 8; ++$i)
    {
      for ($j = 0; $j < 8; ++$j)
      {
        $figure = $boardArray[$i][$j];
        if ($figure == NULL) continue;

        /** @var $figureObject Figure */
        $figureObject = FigureFactory::{$figure->name}($figure->color, new Cords($figure->cords->x, $figure->cords->y));

        /**
         * @var $figure Pawn;
         * @var $figureObject Pawn
         */
        if ($figureObject->name == 'Pawn') $figureObject->firstStep = $figure->firstStep;

        $this->setFigureOnBoard($figureObject);
      }
    }

    return true;
  }

  /**
   * Метод сохраняет состояние игры в state.json
   */
  public function save()
  {
    $date = (object)[];

    $date->boardArray = $this->boardArray;
    $date->darkKing = $this->darkKing;
    $date->whiteKing = $this->whiteKing;
    $date->goingColor = $this->goingColor;

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/state.json', json_encode($date));
  }

  /**
   * Получить фигуру по координатам.
   * Если фигуры нет, возвращает NULL
   * @param $position Cords
   * @return Figure
   */
  public function getFigure($position)
  {
    return $this->boardArray[$position->y][$position->x];
  }

  /**
   * Метод проверяет поставлен ли мат королю
   */
  private function checkMat()
  {
    /**
     * Королю поставлен мат, если он не может переместиться ни на одну из соседних клеток
     * Или его нельзя перекрыть ни одной фигурой
     */

    $king = $this->getCurrentKing();

    for ($x = -1; $x < 2; ++$x)
    {
      for ($y = -1; $y < 2; ++$y)
      {
        if ($x == 0 && $y == 0) continue;

        $cords = new Cords($king->cords->x + $x, $king->cords->y + $y);

        $shah = $this->checkShahOnCords($king, $cords);
        $canMoveKing = $this->canMoveFigure($king, $cords)->success;

        if (!$shah && $canMoveKing)
          return false;
      }
    }

    $color = $king->color == 'white' ? 'dark' : 'white';
    $threat = $this->getFigureArrayCanCome($king->cords, $color);

    $movementVector = $this->getMovementVector($threat[0], $king->cords);

    $y = $threat[0]->cords->y + $movementVector->y;
    $x = $threat[0]->cords->x + $movementVector->x;

    for (;$x != $king->cords->x + $movementVector->x || $y != $king->cords->y + $movementVector->y; $x += $movementVector->x, $y += $movementVector->y)
    {
      for ($i = 0; $i < 8; ++$i)
      {
        for ($j = 0; $j < 8; ++$j)
        {
          $figure = $this->boardArray[$i][$j];

          if ($figure != NULL && $figure->color == $king->color && $this->canMoveFigure($figure, new Cords($x, $y))->success)
          {
            return false;
          }
        }
      }
    }

    return true;
  }

  /**
   * Метод возвращает нормализованный вектор перемещения фигуры до указанной позиции
   * @param $figure Figure Фигура
   * @param $position Cords Позиция перемещения
   * @return Cords
   */
  private function getMovementVector($figure, $position)
  {
    $dx = $position->x - $figure->cords->x;
    $dy = $position->y - $figure->cords->y;

    $nx = $dx != 0 ? $dx / abs($dx) : 0;
    $ny = $dy != 0 ? $dy / abs($dy) : 0;

    return new Cords($nx, $ny);
  }

  /**
   * Устанавливает фигуру на ее место
   * @param $figure Figure
   */
  private function setFigureOnBoard($figure)
  {
    $this->boardArray[$figure->cords->y][$figure->cords->x] = $figure;

    if ($figure->name == 'King')
    {
      if ($figure->color == 'dark') $this->darkKing = $figure;
      else $this->whiteKing = $figure;
    }
  }

  /**
   * Метод реализует логику перемещения пешки по диагонали
   * true - можно идти
   * false - нельзя
   * @param $figure Pawn
   * @param $position Cords
   * @return bool
   */
  private function coursePawn($figure, $position)
  {
    $dx = abs($figure->cords->x - $position->x);
    $dy = $position->y - $figure->cords->y;

    if ($dx == 1 && $dy == $figure->direction)
    {

      $target = $this->getFigure($position);

      if ($target == NULL)
        return false;
      else if ($target->color == $figure->color)
        return false;

      return true;
    }



    if (!$figure->canMove($position->x, $position->y))
      return false;

    $target = $this->getFigure($position);

    return $target == NULL;
  }

  /**
   * Метод проверяет поставлен ли шах королю
   * Возвращает true если шах поставлен
   */
  private function checkShah()
  {
    $king = $this->getCurrentKing();

    return $this->checkShahOnCords($king, $king->cords);
  }

  /**
   * @return King
   */
  private function getCurrentKing()
  {
    if ($this->goingColor == 'white')
      return $this->whiteKing;

    return $this->darkKing;
  }

  /**
   * Определяет будет ли поставлен шах на указанной позиции
   * Возвращает true если шах поставлен
   * @param $king King
   * @param $position Cords
   * @return bool
   */
  private function checkShahOnCords($king, $position)
  {
    $color = $king->color == 'white' ? 'dark' : 'white';
    $figures = $this->getFigureArrayCanCome($position, $color);

    return count($figures) != 0;
  }

  /**
   * Возвращает массив фигур, которые смогут походить на указанные координаты
   * @param $position Cords
   * @param $color string проверяемый цвет
   * @return Figure[]
   */
  private function getFigureArrayCanCome($position, $color)
  {
    $response = [];

    for ($i = 0; $i < 8; ++$i)
    {
      for ($j = 0; $j < 8; ++$j)
      {
        $target = $this->boardArray[$i][$j];

        if ($target != NULL && $target->color == $color && $this->canMoveFigure($target, $position, false)->success)
          array_push($response, $target);
      }
    }

    return $response;
  }

  /**
   * Метод проверяет столкновения фигур
   * true - столкновение найдено
   * false - столкновение не найдено
   * @param $figure Figure
   * @param $position Cords
   * @return boolean
   */
  private function collisionDetect($figure, $position)
  {
    $vecMovement = $this->getMovementVector($figure, $position);

    $stepX = $vecMovement->x;
    $stepY = $vecMovement->y;

    $enemyCounter = 0;
    $tempX = $figure->cords->x + $stepX;
    $tempY = $figure->cords->y + $stepY;

    for ($x = $tempX, $y = $tempY; $x != $position->x + $stepX || $y != $position->y + $stepY; $x += $stepX, $y += $stepY)
    {
      $cords = new Cords($x, $y);
      $target = $this->getFigure($cords);

      if ($target != NULL)
      {
        if ($target->color == $figure->color)
          return true;
        else if ($enemyCounter > 0)
          return true;
        else
          ++$enemyCounter;
      }
    }

    return false;
  }

  /**
   * Метод генерирует объект для ответа
   * @param $success boolean
   * @param $message string
   * @return object
   */
  private function getResponse($success, $message)
  {
    $response = (object)[];

    $response->success = $success;
    $response->error = $message;

    return $response;
  }
}
# API
## Сделать ход
На странице /api/moveFigure.php можно сделать ход фигурой.
Она принимает 4 параметра, которые передаются GET запросом.

| Параметр | Описание                                 |
| :------: | :--------------------------------------: |
| figure_x | Положение ходящей фигуры по координате x |
| figure_y | Положение ходящей фигуры по координате y |
| pos_x    | X Координата точки перемещения           |
| pos_y    | Y Координата точки перемещения           |

## Статус партии
Узнать информацию о партии можно на странице /api/getState.php 
## Начать новую партию
Новую партию можно начать на странице /api/newGame.php

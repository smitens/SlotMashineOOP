<?php
include ("SlotMachineClases.php");

$symbols = [
    new Symbol("9", 2, 2),
    new Symbol("*", 2, 4),
    new Symbol("#", 2, 8),
    new Symbol("@", 4, 9),
    new Symbol("$", 6, 10),
];

$winConditions = [
    // Horizontal
    [[0, 0], [0, 1], [0, 2]],
    [[1, 0], [1, 1], [1, 2]],
    [[2, 0], [2, 1], [2, 2]],
    // Vertical
    [[0, 0], [1, 0], [2, 0]],
    [[0, 1], [1, 1], [2, 1]],
    [[0, 2], [1, 2], [2, 2]],
    // Diagonal
    [[0, 0], [1, 1], [2, 2]],
    [[0, 2], [1, 1], [2, 0]],
];


$game = new Game(2, 3, 3, $winConditions, $symbols);
$game->play();

<?php
class Symbol
{
    public string $value;
    public int $multiplier;
    public int $occurrence;
    public int $row;
    public int $column;

    public function __construct(string $value, int $multiplier, int $occurrence, int $row = -1, int $column = -1)
    {
        $this->value = $value;
        $this->multiplier = $multiplier;
        $this->occurrence = $occurrence;
        $this->row = $row;
        $this->column = $column;
    }
}

class Board
{
    private array $symbols;
    private array $board;

    public function __construct(array $symbols)
    {
        $this->symbols = $symbols;
        $this->board = [];
    }

    public function generateBoard(int $rows, int $columns): array
    {
        $this->board = [];
        for ($row = 0; $row < $rows; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                $symbol = $this->getRandomSymbol();
                $symbolInstance = clone $symbol;
                $symbolInstance->row = $row;
                $symbolInstance->column = $column;
                $this->board[$row][$column] = $symbolInstance;
            }
        }
        return $this->board;
    }

    private function getRandomSymbol(): Symbol
    {
        $randomSymbol = rand(1, 10);
        foreach ($this->symbols as $symbol) {
            if ($randomSymbol <= $symbol->occurrence) {
                return $symbol;
            }
            $randomSymbol -= $symbol->occurrence;
        }
        return end($this->symbols);
    }

    public function displayBoard()
    {
        echo "\n";
        foreach ($this->board as $row) {
            foreach ($row as $cell) {
                echo $cell->value . " ";
            }
            echo "\n";
        }
    }

    public function getBoard(): array
    {
        return $this->board;
    }
}

class Game
{
    private int $baseBet;
    private int $coins;
    private int $rows;
    private int $columns;
    private int $bet;
    private array $winConditions;
    private array $symbols;

    public function __construct(int $baseBet, int $rows, int $columns, array $winConditions, array $symbols)
    {
        $this->baseBet = $baseBet;
        $this->rows = $rows;
        $this->columns = $columns;
        $this->winConditions = $winConditions;
        $this->symbols = $symbols;
    }

    public function play(): void
    {
        echo "Welcome to the Slot Machine Game!\n";
        echo "\n";
        $this->initializeCoinAmount();
        do {
            $this->placeBet();
            $gameBoard = new Board($this->symbols);
            $gameBoard->generateBoard($this->rows, $this->columns);
            $gameBoard->displayBoard();
            $payout = $this->calculatePayout($gameBoard->getBoard());
            $this->updateCoinAmount($payout);
            $this->handleEndOfSpin();
        } while ($this->coins >= $this->baseBet);
    }

    private function initializeCoinAmount(): void
    {
        do {
            $this->coins = (int)readline("Enter start amount of coins to play (min {$this->baseBet}): ");
        } while ($this->coins < $this->baseBet);
    }

    private function placeBet(): void
    {
        do {
            echo "\nAvailable coin amount: {$this->coins}\nBase bet is {$this->baseBet}.\n";
            $this->bet = (int)readline("Enter your bet amount (min {$this->baseBet}): ");
        } while ($this->bet < $this->baseBet || $this->bet > $this->coins);
    }

    private function calculatePayout(array $board): int
    {
        $payout = 0;
        foreach ($this->symbols as $symbol) {
            foreach ($this->winConditions as $condition) {
                $score = 0;
                foreach ($condition as $position) {
                    list($row, $column) = $position;
                    if (isset($board[$row][$column]) && $board[$row][$column]->value === $symbol->value) {
                        $score++;
                    }
                }
                if ($score === count($condition)) {
                    $payout += $this->bet * $symbol->multiplier;
                }
            }
        }
        return $payout;
    }

    private function updateCoinAmount(int $payout)
    {
        $this->coins = $this->coins + $payout - $this->bet;
        echo "\nYour coin amount: {$this->coins}\nBet: {$this->bet}\nPayout: {$payout}\n";
    }

    private function handleEndOfSpin()
    {
        if ($this->coins < $this->baseBet) {
            echo "You are out of coins...\n";
            do {
                $addFunds = readline("Add more coins? yes/no: ");
                if (strtolower($addFunds) === "no") {
                    exit("\nThanks for playing!\n");
                }
            } while ($addFunds !== "yes");
            $this->initializeCoinAmount();
        }
        do {
            $continuePlaying = readline("Do you want to continue playing? yes/no: ");
            if (strtolower($continuePlaying) === "no") {
                exit("\nYour balance: {$this->coins}\nThanks for playing!\n");
            }
        } while ($continuePlaying !== "yes");
    }
}
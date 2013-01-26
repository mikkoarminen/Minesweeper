<?php

namespace Loiste\MinesweeperBundle\Model;

/**
 * This class represents a game model.
 */
class Game
{
    const ROW_COUNT = 10;
    const COL_COUNT = 20;
    const MINE_COUNT = 20;

    const STATE_BEGINNING = 0;
    const STATE_RUNNING = 1;
    const STATE_READY = 2;
    const STATE_GAME_OVER = 3;

    /**
     * A two dimensional array of game objects.
     *
     * E.x.: $gameArea[3][2] instance of GameObject
     *
     * @var array
     */
    public $gameArea;
    /**
     * State of the game.
     * 
     * Possible values: Game::STATE_BEGINNING, Game::STATE_RUNNING,
     * Game::STATE_READY, Game::STATE::GAME_OVER
     * 
     * @var int
     */
    private $state;
    /**
     * Total number of mines in the game area.
     * @var int
     */
    private $mineCount;
    /**
     * Number of cell rows.
     * @var int
     */
    private $rows;
    /**
     * Number of cell columns.
     * @var int
     */
    private $columns;
    /**
     * Number of cell that haven't been opened.
     * @var int
     */
    private $undiscoveredCellCount;

    /**
     * Initializes the game object.
     * 
     * @param int $rows
     * @param int $columns
     * @param int $mineCount
     */
    public function __construct($rows=Game::ROW_COUNT, 
        $columns=Game::COL_COUNT, $mineCount=Game::MINE_COUNT)
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->mineCount = $mineCount;
        
        $this->state = Game::STATE_BEGINNING;
        
        $this->undiscoveredCellCount = $rows * $columns;
        
        // Upon constructing a new game instance, setup an empty game area.
        $this->gameArea = array();

        // Initialize game area with empty cells.
        for ($row = 0; $row < $rows; $row++) {

            $temp = array();
            for ($column = 0; $column < $columns; $column++) {
                $temp[] = new GameObject();
            }
            
            $this->gameArea[] = $temp;
        }
        
        // Add mines.
        for ($mine = 0; $mine < $this->mineCount; $mine++) {
            $mineAdded = false;
            do {
                $r = mt_rand(0, $rows-1);
                $c = mt_rand(0, $columns-1);
                
                if ($this->gameArea[$r][$c]->type != GameObject::TYPE_MINE) {
                    $this->gameArea[$r][$c]->type = GameObject::TYPE_MINE;
                    $mineAdded = true;
                }
                
            } while ($mineAdded == false);
        }
    }
    
    /**
     * Returns the state of the game object.
     * 
     * @return int As defined in Game::STATE_*
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * Sets cell marked or removes the mark added earlier.
     * 
     * @param int $row
     * @param int $column
     */
    public function markCell($row, $column)
    {
        switch ($this->gameArea[$row][$column]->type) {
            case GameObject::TYPE_UNDISCOVERED:
                $this->gameArea[$row][$column]->type = GameObject::TYPE_MARKED_UNDISCOVERED;
                break;
            case GameObject::TYPE_MINE:
                $this->gameArea[$row][$column]->type = GameObject::TYPE_MARKED_MINE;
                break;
            case GameObject::TYPE_MARKED_MINE:
                $this->gameArea[$row][$column]->type = GameObject::TYPE_MINE;
                break;
            case GameObject::TYPE_MARKED_UNDISCOVERED:
                $this->gameArea[$row][$column]->type = GameObject::TYPE_UNDISCOVERED;
                break;
        }
    }
    
    /**
     * Opens the given cell. If the given cell is empty (doesn't have
     * surrounding mines) the surrounding cells are opened recursively.
     * 
     * Opening a cell with mine causes the game to end.
     * 
     * If number cell is "opened" and the value of the cell matches to
     * the number of the surrounding marks, the remaining undiscovered
     * surrounding cells are opened.
     * 
     * @param int $row
     * @param int $column
     */
    public function openCell($row, $column)
    {
        if ($this->state == Game::STATE_BEGINNING) {
            $this->state = Game::STATE_RUNNING;
        }
                
        // Open an empty undiscovered cell.
        if ($this->gameArea[$row][$column]->type == GameObject::TYPE_UNDISCOVERED) {
            
            $this->undiscoveredCellCount--;
            
            // Get the number of mines around the given cell.
            $mineCount = $this->getMineCount($row, $column);
            
            // If there are no mines around the cell it should be opened 
            // as an empty cell and recursive calls should be made to 
            // open the surrounding cells.
            if ( $mineCount == 0) {
                $this->gameArea[$row][$column]->type = GameObject::TYPE_EMPTY;

                // Open all surrounding cells.
                for ($r = $row-1; $r <= $row+1; $r++) {
                    for ($c = $column-1; $c <= $column+1; $c++) {
                        // Open cell only if indexes are valid and given 
                        // cell is not the current cell.
                        if (($r >= 0 && $r < $this->rows) &&
                            ($c >= 0 && $c < $this->columns) &&
                            !($r == $row && $c == $column)) {
                                
                            $this->openCell($r, $c);
                        }
                        
                    }
                }
                
            }
            // If surrounding mines exist the cell should be opened as
            // a number cell.
            else {
                $this->gameArea[$row][$column]->type = GameObject::TYPE_NUMBER;
                $this->gameArea[$row][$column]->number = $mineCount;
            }
        
        } 
        // Open cell with mine.
        elseif ($this->gameArea[$row][$column]->type == GameObject::TYPE_MINE) {
            // KABOOM!
            
            // Show clicked cell as explosion.
            $this->gameArea[$row][$column]->type = GameObject::TYPE_EXPLOSION;
            // Set other mine cells as discovered.
            for ($r = 0; $r < $this->rows; $r++) {
                for ($c = 0; $c < $this->columns; $c++) {
                    if ($this->gameArea[$r][$c]->type == GameObject::TYPE_MINE) {
                        $this->gameArea[$r][$c]->type = GameObject::TYPE_MINE_DISCOVERED;
                    }
                }
            }
            
            $this->state = Game::STATE_GAME_OVER;
        }
        // If number cell is clicked the surrounding cells should be opened
        // when the number of surrounding cells with marks matches to
        // the value of the number field.
        elseif ($this->gameArea[$row][$column]->type == GameObject::TYPE_NUMBER) {
            $surroundingMarkCount = $this->getSurroundingMarks($row, $column);
            
            if ($surroundingMarkCount == $this->gameArea[$row][$column]->number) {
                // Open all surrounding cells not marked as mine.
                for ($r = $row-1; $r <= $row+1; $r++) {
                    for ($c = $column-1; $c <= $column+1; $c++) {
                        // Open cell only if indexes are valid and given 
                        // cell is not the current cell and the cell is not
                        // marked as mine.
                        if (($r >= 0 && $r < $this->rows) &&
                            ($c >= 0 && $c < $this->columns) &&
                            !($r == $row && $c == $column) &&
                            ($this->gameArea[$r][$c]->type != GameObject::TYPE_MARKED_MINE) &&
                            ($this->gameArea[$r][$c]->type != GameObject::TYPE_MARKED_UNDISCOVERED) &&
                            ($this->gameArea[$r][$c]->type != GameObject::TYPE_NUMBER)) {
                                
                            $this->openCell($r, $c);
                        }
                        
                    }
                }
                
            }
        }
        
        // Check if all the discoverable cells have been opened.
        if ($this->isReady()) {
            $this->state = Game::STATE_READY;
        }
        
    }
    
    /**
     * Returns if all the discoverable cells have been opened.
     * 
     * @return bool
     */
    private function isReady() {
        return ($this->undiscoveredCellCount == $this->mineCount);
    }
    
    /**
     * Returns the number of the marked cells around the given cell.
     * 
     * @param int $row
     * @param int $column
     * 
     * @return int
     */
    private function getSurroundingMarks($row, $column)
    {
        $markedCount = 0;
        
        // Test all surrounding cells for existence of a mark.
        for ($r = $row-1; $r <= $row+1; $r++) {
            for ($c = $column-1; $c <= $column+1; $c++) {
                // Only test for valid indexes.
                // Don't check the middle cell.
                if (($r >= 0 && $r < $this->rows) &&
                    ($c >= 0 && $c < $this->columns) &&
                    !($r == $row && $c == $column)) {
                        
                    if ($this->gameArea[$r][$c]->isMarked()) {
                        $markedCount++;
                    }
                }
                
            }
        }
                
        return $markedCount;
    }
    
    /**
     * Returns the number of mines around the given cell.
     * 
     * @param int $row
     * @param int $column
     * 
     * @return int
     */     
    private function getMineCount($row, $column) 
    {
        $mineCount = 0;
        
        // Test all surrounding cells for existence of a mine.
        for ($r = $row-1; $r <= $row+1; $r++) {
            for ($c = $column-1; $c <= $column+1; $c++) {
                // Only test for valid indexes.
                // Don't check the middle cell.
                if (($r >= 0 && $r < $this->rows) &&
                    ($c >= 0 && $c < $this->columns) &&
                    !($r == $row && $c == $column)) {
                        
                    if ($this->gameArea[$r][$c]->hasMine()) {
                        $mineCount++;
                    }
                }
                
            }
        }
                
        return $mineCount;
    }
    
}

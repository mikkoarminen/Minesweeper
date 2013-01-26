<?php

namespace Loiste\MinesweeperBundle\Model;

/**
 * This class represents a game object.
 */
class GameObject
{
    const TYPE_UNDISCOVERED = 0;
    const TYPE_MINE = 1;

    const TYPE_EMPTY = 2; // Discovered.
    const TYPE_NUMBER = 3; // Discovered.
    const TYPE_EXPLOSION = 4; // Damn we hit a mine!
    const TYPE_MINE_DISCOVERED = 5;
    
    const TYPE_MARKED_UNDISCOVERED = 6;
    const TYPE_MARKED_MINE = 7;

    public $type;
    public $number;

    public function __construct($type = 0)
    {
        $this->type = $type;
    }

    public function hasMine()
    {
        return ($this->type == GameObject::TYPE_MINE or
            $this->type == GameObject::TYPE_EXPLOSION or
            $this->type == GameObject::TYPE_MINE_DISCOVERED or
            $this->type == GameObject::TYPE_MARKED_MINE);
    }

    public function isMarked()
    {
        return ($this->type == GameObject::TYPE_MARKED_UNDISCOVERED or
            $this->type == GameObject::TYPE_MARKED_MINE);
    }

    /**
     * Returns the number of mines around this cell.
     */
    public function getNumber()
    {
        return $this->number;
    }
}

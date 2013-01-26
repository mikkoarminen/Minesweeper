<?php

namespace Loiste\MinesweeperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Loiste\MinesweeperBundle\Model\Game;

class GameController extends Controller
{
    private $session;
    private $game;
    
    function __construct() 
    {
        $this->session = new Session();
        $this->session->start();
    }
    
    private function getResponse()
    {
        return $this->render('LoisteMinesweeperBundle:Default:index.html.twig', array(
            'gameArea' => $this->game->gameArea,
            'gameState' => $this->game->getState()
        ));
    }
    
    public function startAction()
    {
        // Setup an empty game. To keep things very simple for candidates, we just store info on the session.
        $this->game = new Game();
        $this->session->set('game', $this->game);
        return $this->getResponse();
    }

    public function makeMoveAction()
    {
        $row = $this->getRequest()->get('row'); // Retrieves the row index.
        $column = $this->getRequest()->get('column'); // Retrieves the column index.

        $this->game = $this->session->get('game'); /** @var $game Game */
        
        $state = $this->game->getState();
        if ($state == Game::STATE_BEGINNING or $state == Game::STATE_RUNNING) {
            $this->game->openCell($row, $column);
        }

        return $this->getResponse();
    }
    
    public function markCellAction()
    {
        $row = $this->getRequest()->get('row'); // Retrieves the row index.
        $column = $this->getRequest()->get('column'); // Retrieves the column index.

        $this->game = $this->session->get('game'); /** @var $game Game */
        
        $state = $this->game->getState();
        if ($state == Game::STATE_BEGINNING or $state == Game::STATE_RUNNING) {
            $this->game->markCell($row, $column);
        }

        return $this->getResponse();
    }
    
    
    
}

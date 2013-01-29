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
            'gameState' => $this->game->getState(),
            'mineDensity' => $this->game->getMineDensity(),
        ));
    }
    
    public function startAction()
    {
        $game = $this->session->get('game');
        // Use the previously selected mine density if exists.
        if ($game) {
            $density = $game->getMineDensity();
            $this->game = new Game(Game::ROW_COUNT, Game::COL_COUNT, $density);
        } else {
            $this->game = new Game();
        }
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
    
    public function setDensityAction()
    {
        $density = $this->getRequest()->get('density');

        $this->game = $this->session->get('game'); /** @var $game Game */

        $state = $this->game->getState();

        // Setup an empty game with new mine density.
        $this->game = new Game(Game::ROW_COUNT, Game::COL_COUNT, $density/100);
        $this->session->set('game', $this->game);

        return $this->getResponse();
    }
        
}

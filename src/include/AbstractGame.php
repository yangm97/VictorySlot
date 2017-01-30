<?php
namespace game;

/**
 * Created by DevMahno
 */

abstract class AbstractGame
{
    abstract public function gameType();
    abstract public function playGame();
}

abstract class GameType {
    const UNKNOWN = 0;
    const FRUIT_SLOT = 1;
}

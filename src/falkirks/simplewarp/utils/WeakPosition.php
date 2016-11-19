<?php
namespace falkirks\simplewarp\utils;


use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Server;

/**
 * This is hacky solution to the problem
 * and won't work if other classes use the
 * "level" field.
 * Class WeakPosition
 * @package falkirks\simplewarp\utils
 */
class WeakPosition extends Position{
    public $level;
    public $levelName;

    public function __construct($x = 0, $y = 0, $z = 0, $levelName){
        parent::__construct($x, $y, $z, null);
        $this->levelName = $levelName;
    }
    public function isValid(): bool {
        return Server::getInstance()->getLevelByName($this->levelName) instanceof Level;
    }
    public function updateProperties(){
        $this->level = $this->getLevel();
    }
    public function getLevel(): Level{
        return Server::getInstance()->getLevelByName($this->levelName);
    }

    public function getLevelName(){
        return $this->levelName;
    }
}
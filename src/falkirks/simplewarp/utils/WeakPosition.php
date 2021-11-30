<?php
namespace falkirks\simplewarp\utils;


use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\Server;

/**
 * This is hacky solution to the problem
 * and won't work if other classes use the
 * "level" field.
 * Class WeakPosition
 * @package falkirks\simplewarp\utils
 */
class WeakPosition extends Position{
    public $world;
    public $worldName;

    public function __construct(float $x = 0, float $y = 0, float $z = 0, $levelName){
        parent::__construct($x, $y, $z, null);
        $this->worldName = $levelName;
    }
    public function isValid(): bool {
        return Server::getInstance()->getWorldManager()->getWorldByName($this->worldName) instanceof World;
    }
    public function updateProperties(){
        $this->world = $this->getWorld();
    }
    public function getWorld(): World{
        return Server::getInstance()->getWorldManager()->getWorldByName($this->worldName);
    }

    public function getWorldName(){
        return $this->worldName;
    }
}
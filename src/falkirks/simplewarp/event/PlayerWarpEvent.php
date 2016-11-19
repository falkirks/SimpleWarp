<?php
namespace falkirks\simplewarp\event;


use falkirks\simplewarp\Destination;
use falkirks\simplewarp\Warp;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PlayerWarpEvent extends PlayerEvent implements Cancellable{
    public static $handlerList = null;
    /** @var  Warp */
    private $warp;
    /** @var Destination */
    private $destination = null;
    public function __construct(Player $player, Warp $warp){
        $this->player = $player;
        $this->warp = $warp;
    }

    /**
     * @return Warp
     */
    public function getWarp(): Warp{
        return $this->warp;
    }
    public function getDestination(): Destination{
        return ($this->destination instanceof Destination ? $this->destination : $this->warp->getDestination());
    }
    public function setDestination(Destination $destination){
        $this->destination = $destination;
    }

}
<?php
namespace falkirks\simplewarp\event;


use falkirks\simplewarp\Warp;
use pocketmine\event\Event;

class WarpEvent extends Event{
    /** @var  Warp */
    protected $warp;
    public function __construct(Warp $warp){
        $this->warp = $warp;
    }

    /**
     * @return Warp
     */
    public function getWarp(): Warp{
        return $this->warp;
    }
}
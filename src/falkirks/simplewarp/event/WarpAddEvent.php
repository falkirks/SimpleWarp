<?php
namespace falkirks\simplewarp\event;


use falkirks\simplewarp\Warp;
use pocketmine\command\CommandSender;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;

class WarpAddEvent extends Event implements  Cancellable{
    public static $handlerList = null;
    /** @var  Warp */
    private $warp;
    /** @var CommandSender  */
    private $sender;
    public function __construct(CommandSender $sender, Warp $warp){
        $this->sender = $sender;
        $this->warp = $warp;
    }

    /**
     * @return Warp
     */
    public function getWarp(){
        return $this->warp;
    }

    /**
     * @return CommandSender
     */
    public function getSender(){
        return $this->sender;
    }

}
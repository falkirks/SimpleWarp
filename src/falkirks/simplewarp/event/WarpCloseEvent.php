<?php
namespace falkirks\simplewarp\event;


use falkirks\simplewarp\Warp;
use pocketmine\command\CommandSender;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class WarpCloseEvent extends WarpEvent implements  Cancellable{

    use CancellableTrait;

    public static $handlerList = null;
    /** @var CommandSender  */
    private $sender;
    public function __construct(CommandSender $sender, Warp $warp){
        parent::__construct($warp);
        $this->sender = $sender;
    }
    /**
     * @return CommandSender
     */
    public function getSender(): CommandSender{
        return $this->sender;
    }

}
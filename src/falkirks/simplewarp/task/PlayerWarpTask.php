<?php
namespace falkirks\simplewarp\task;


use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\Warp;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class PlayerWarpTask extends PluginTask{
    private $warp;
    private $player;
    private $position;
    public function __construct(SimpleWarp $plugin, Warp $warp, Player $player){
        parent::__construct($plugin);
        $this->warp = $warp;
        $this->player = $player;
        $this->position = $player->getPosition();
    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun($currentTick){
        if($this->player instanceof Player && $this->player->isOnline()){
            if(!$this->getOwner()->getConfig()->get("hold-still-enabled") || $this->player->getPosition()->equals($this->position)){ //FIXME edge-case if teleported to same coordinates in another world
                $this->warp->teleport($this->player);
            }
        }
    }

    /**
     * @return Warp
     */
    public function getWarp(): Warp{
        return $this->warp;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player{
        return $this->player;
    }


}
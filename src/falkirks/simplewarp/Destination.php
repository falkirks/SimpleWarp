<?php

namespace falkirks\simplewarp;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\utils\WeakPosition;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Destination{
    /** @var Position  */
    protected $position;
    protected $address;
    protected $port;
    protected $message;

    public function __construct(...$params){
        if(is_array($params[0])) $params = $params[0];
        if(isset($params[0])){
            if($params[0] instanceof Position){
                $this->position = $params[0];
                $this->message = (isset($params[1]) ? $params[1] : 
        else{
            throw new /BadMethodException

    }
    public function teleport(Player $player){
        if($this->message !== null){
            $player->sendMessage($this->message);
        }

        if($this->position instanceof Position){
            if($this->position->isValid()) {
                if($this->position instanceof WeakPosition){
                    $this->position->updateProperties();
                }
                //Server::getInstance()->getLogger()->info($this->position->x . " : " . $this->position->y . " : " . $this->position->z);
                $player->teleport($this->position);
            }
            else{
                $player->sendMessage($this->getApi()->executeTranslationItem("level-not-loaded-warp"));
            }
        }
        
    }
    public function isInternal(): bool{
        return $this->position instanceof Position;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position{
        return $this->position;
    }

    /**
     * @return mixed
     */
    public function getAddress(){
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getPort(){
        return $this->port;
    }
    public function toString(){
        if($this->isInternal()) {
            if($this->position instanceof WeakPosition){
                $levelName = $this->position->levelName;
            }
            else{
                $levelName = $this->position->getLevel()->getName();
            }
            if($this->getApi()->getConfigItem("display-exact-coordinates")) {
                return "(X: {$this->getPosition()->x}, Y: {$this->getPosition()->y}, Z: {$this->getPosition()->z}, LEVEL: {$levelName}) ";
            }
            else{
                return "(X: {$this->getPosition()->getFloorX()}, Y: {$this->getPosition()->getFloorY()}, Z: {$this->getPosition()->getFloorZ()}, LEVEL: " . $levelName . ")";
            }
        }
      }

    /**
     * @return SimpleWarpApi
     */
    protected function getApi(): SimpleWarpAPI{
        return Server::getInstance()->getPluginManager()->getPlugin("SimpleWarp")->getApi();
    }

}

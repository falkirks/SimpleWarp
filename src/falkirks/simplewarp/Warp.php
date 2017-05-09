<?php
namespace falkirks\simplewarp;

use falkirks\simplewarp\event\PlayerWarpEvent;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

/**
 * The class that drives it all.
 *
 * Class Warp
 * @package falkirks\simplewarp
 */
class Warp implements \JsonSerializable {
    protected $manager;
    protected $name;
    protected $destination;
    protected $isPublic;
    protected $metadata;

    public function __construct(WarpManager $manager, $name, Destination $destination, $isPublic = false, $metadata = []){
        $this->manager = $manager;
        $this->name = $name;
        $this->destination = $destination;
        $this->isPublic = $isPublic;
        $this->metadata = $metadata;
        SimpleWarpPermissions::setupPermission($this);
    }
    public function teleport(Player $player){
        $ev = new PlayerWarpEvent($player, $this);
        $this->getServer()->getPluginManager()->callEvent($ev);
        if($ev->isCancelled()){
            return;
        }
        $ev->getDestination()->teleport($player);
    }
    public function canUse(CommandSender $player): bool{
        return ($this->isPublic || $player->hasPermission(SimpleWarpPermissions::BASE_WARP_PERMISSION) || $player->hasPermission(SimpleWarpPermissions::BASE_WARP_PERMISSION . "." . $this->name));
    }

    /**
     * @param boolean $isPublic
     */
    public function setPublic($isPublic = true){
        $this->isPublic = $isPublic;
        $this->getManager()->offsetSet($this->name, $this);
    }

    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return Destination
     */
    public function getDestination(): Destination{
        return $this->destination;
    }

    /**
     * @param Destination $destination
     */
    public function setDestination(Destination $destination){
        $this->destination = $destination;
        $this->getManager()->offsetSet($this->name, $this);
    }

    /**
     * @param WarpManager $manager
     */
    public function setManager(WarpManager $manager){
        $this->manager = $manager;
        $this->getManager()->offsetSet($this->name, $this);
    }

    /**
     * @return array
     */
    public function getAllMetadata(): array{
        return $this->metadata;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getMetadata($key){
        return $this->metadata[$key] ?? null;
    }
    /**
     * @param $key
     * @param $value
     */
    public function setMetadata($key, $value){
        $this->metadata[$key] = $value;
        $this->getManager()->offsetSet($this->name, $this);
    }

    /**
     * @return boolean
     */
    public function isPublic(): bool {
        return $this->isPublic;
    }
    private function getServer(): Server{
        return Server::getInstance();
    }

    /**
     * @return WarpManager
     */
    public function getManager(): WarpManager{
        return $this->manager;
    }

    /**
     * For debugging use
     * @return array
     */
    public function jsonSerialize(){
        return [
            'dest' => $this->getDestination()->toString(),
            'isPublic' => $this->isPublic(),
            'metadata' => $this->metadata
        ];
    }


}
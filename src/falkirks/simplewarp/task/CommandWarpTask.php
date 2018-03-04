<?php
namespace falkirks\simplewarp\task;


use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\Warp;
use pocketmine\command\CommandSender;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\utils\Random;

class CommandWarpTask extends PlayerWarpTask{
    private $sender;

    public function __construct(SimpleWarp $plugin, Warp $warp, Player $player, CommandSender $sender){
        parent::__construct($plugin, $warp, $player);
        $this->sender = $sender;

        if($this->getOwner()->getConfig()->get("hold-still-enabled")){
            if ($this->player->getName() !== $this->sender->getName()) {
                $this->sender->sendMessage($this->getOwner()->getApi()->executeTranslationItem("hold-still-other"));
            }
            else {
                $this->player->sendPopup($this->getOwner()->getApi()->executeTranslationItem("hold-still-popup"));
            }
        }
    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick){
        if($this->player instanceof Player && $this->player->isOnline()){
            if(!$this->getOwner()->getConfig()->get("hold-still-enabled") || $this->player->getPosition()->equals($this->position)) {

                $this->player->sendPopup($this->getOwner()->getApi()->executeTranslationItem("warping-popup", $this->warp->getName()));

                $this->warp->teleport($this->player);

                $this->displaySmoke($this->position);

                if ($this->player->getName() !== $this->sender->getName()) {
                    $this->sender->sendMessage($this->getOwner()->getApi()->executeTranslationItem("other-player-warped", $this->player->getName(), $this->warp->getName()));
                }
                else {
                    $this->sender->sendMessage($this->getOwner()->getApi()->executeTranslationItem("warp-done"));
                }
            }
            else{
                if ($this->player->getName() !== $this->sender->getName()) {
                    $this->sender->sendMessage($this->getOwner()->getApi()->executeTranslationItem("hold-still-cancelled-other"));
                }
                else {
                    $this->sender->sendMessage($this->getOwner()->getApi()->executeTranslationItem("hold-still-cancelled"));
                }
            }
        }
    }

    public function displaySmoke(Position $pos){
        //particle smoke 120 71 124 1 1 1 35 200
        $random = new Random((int) (microtime(true) * 1000) + mt_rand());

        $particle = new SmokeParticle(new Vector3($pos->x, $pos->y + 0.7, $pos->z), 200);
        for($i = 0; $i < 35; ++$i){
            $particle->setComponents(
                $pos->x + $random->nextSignedFloat(),
                $pos->y + $random->nextSignedFloat(),
                $pos->z + $random->nextSignedFloat()
            );
            $pos->getLevel()->addParticle($particle);
        }
    }

}

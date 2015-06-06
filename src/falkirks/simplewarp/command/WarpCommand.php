<?php
namespace falkirks\simplewarp\command;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class WarpCommand extends Command implements PluginIdentifiableCommand{
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct("warp", "Warp around your world.", "/warp <name> [player]");
        $this->api = $api;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if($sender->hasPermission(SimpleWarpPermissions::WARP_COMMAND)){
            if(isset($args[0])){
                if(isset($this->api->getWarpManager()[$args[0]])) {
                    if (isset($args[1])) {
                        if($sender->hasPermission(SimpleWarpPermissions::WARP_OTHER_COMMAND)) {
                            if (($player = $this->api->getSimpleWarp()->getServer()->getPlayer($args[1])) instanceof Player) {
                                /** @var Warp $warp */
                                $warp = $this->api->getWarpManager()[$args[0]];
                                if ($warp->canUse($sender)) {
                                    $this->displaySmoke($player);
                                    $player->sendPopup("Warping...");
                                    $warp->teleport($player);
                                    $sender->sendMessage($player->getName() . " has been warped to " . TextFormat::AQUA . $args[0] . TextFormat::RESET . ".");
                                }
                                else{
                                    $sender->sendMessage(TextFormat::RED . "You don't have permission to use this warp." . TextFormat::RESET);
                                }
                            }
                            else {
                                $sender->sendMessage("That player doesn't exist.");
                            }
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED . "You don't have permission to warp other players." . TextFormat::RESET);
                        }
                    }
                    elseif ($sender instanceof Player) {
                        /** @var Warp $warp */
                        $warp = $this->api->getWarpManager()[$args[0]];
                        if($warp->canUse($sender)){
                            $this->displaySmoke($sender);
                            $sender->sendPopup("Warping...");
                            $warp->teleport($sender);
                            $sender->sendMessage("You have been warped");
                        }
                        else{
                            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this warp." . TextFormat::RESET);
                        }
                    }
                    else {
                        $sender->sendMessage($this->getUsage());

                    }
                }
                else{
                    $sender->sendMessage(TextFormat::RED . "That warp doesn't exist." . TextFormat::RESET);
                }
            }
            else{
                $sender->sendMessage($this->getUsage());
                Version::sendVersionMessage($sender);
            }
        }
        else{
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command" . TextFormat::RESET);
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

    /**
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin(){
        return $this->api->getSimpleWarp();
    }
}
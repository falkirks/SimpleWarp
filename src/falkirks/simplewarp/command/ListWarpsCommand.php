<?php
namespace falkirks\simplewarp\command;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ListWarpsCommand extends Command implements PluginIdentifiableCommand{
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct("listwarps", "List all your warps.", "/listwarps");
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
        if($sender->hasPermission(SimpleWarpPermissions::LIST_WARPS_COMMAND)){
            $ret = "Warp list:\n";
            /** @var Warp[] $iterator */
            $iterator = $this->api->getWarpManager()->getIterator();
            foreach($iterator as $w){
                if($w->canUse($sender)){
                    $ret .= " * " . $w->getName() .  " ";
                    if($sender->hasPermission(SimpleWarpPermissions::LIST_WARPS_COMMAND_XYZ)){
                        $dest = $w->getDestination();
                        $ret .= $dest->toString();
                    }
                    $ret .= "\n";
                }
            }
            if($sender instanceof Player && $sender->hasPermission(SimpleWarpPermissions::LIST_WARPS_COMMAND_VISUAL) && isset($args[0]) && $args[0] === "v"){
                $sender->sendMessage("Visual display soon.");
                foreach($iterator as $warp){
                    if($warp->getDestination()->isInternal() && $warp->getDestination()->getPosition()->getLevel() === $sender->getLevel()){
                        $particle = new FloatingTextParticle($warp->getDestination()->getPosition(), "(X: {$warp->getDestination()->getPosition()->getFloorX()}}, Y: {$warp->getDestination()->getPosition()->getFloorY()}, Z: {$warp->getDestination()->getPosition()->getFloorZ()}, LEVEL: {$warp->getDestination()->getPosition()->getLevel()->getName()})", "WARP: " . TextFormat::AQUA . $warp->getName() . TextFormat::RESET);
                        $sender->getLevel()->addParticle($particle, [$sender]);
                    }
                }
            }
            $sender->sendMessage(($ret !== "Warp list:\n" ? $ret : TextFormat::RED . "No warps found." . TextFormat::RESET));
        }
        else{
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command" . TextFormat::RESET);
        }
    }


    /**
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin(){
        return $this->api->getSimpleWarp();
    }
}
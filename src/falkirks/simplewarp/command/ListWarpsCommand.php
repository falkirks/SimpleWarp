<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;

class ListWarpsCommand extends SimpleWarpCommand {
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct($api->executeTranslationItem("listwarps-cmd"), $api->executeTranslationItem("listwarps-desc"), $api->executeTranslationItem("listwarps-usage"));
        $this->api = $api;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(parent::execute($sender, $commandLabel, $args)) {
            if ($sender->hasPermission(SimpleWarpPermissions::LIST_WARPS_COMMAND)) {
                $ret = $this->api->executeTranslationItem("listwarps-list-title");
                /** @var Warp[] $iterator */
                $iterator = $this->api->getWarpManager();
                foreach ($iterator as $w) {
                    if ($w->canUse($sender)) {
                        $ret .= " * " . $w->getName() . " ";
                        if ($sender->hasPermission(SimpleWarpPermissions::LIST_WARPS_COMMAND_XYZ)) {
                            $dest = $w->getDestination();
                            $ret .= $dest->toString();
                        }
                        $ret .= "\n";
                    }
                }
                /**
                 * EASTER EGG!
                 */
                if ($sender instanceof Player && $sender->hasPermission(SimpleWarpPermissions::LIST_WARPS_COMMAND_VISUAL) && isset($args[0]) && $args[0] === "v") {
                    foreach ($iterator as $warp) {
                        if ($warp->getDestination()->isInternal() && $warp->getDestination()->getPosition()->getLevel() === $sender->getLevel()) {
                            $particle = new FloatingTextParticle($warp->getDestination()->getPosition(), "(X: {$warp->getDestination()->getPosition()->getFloorX()}}, Y: {$warp->getDestination()->getPosition()->getFloorY()}, Z: {$warp->getDestination()->getPosition()->getFloorZ()}, LEVEL: {$warp->getDestination()->getPosition()->getLevel()->getName()})", "WARP: " . TextFormat::AQUA . $warp->getName() . TextFormat::RESET);
                            $sender->getLevel()->addParticle($particle, [$sender]);
                        }
                    }
                }
                $sender->sendMessage(($ret !== $this->api->executeTranslationItem("listwarps-list-title") ? $ret : $this->api->executeTranslationItem("listwarps-no-warps")));
            }
            else {
                $sender->sendMessage($this->api->executeTranslationItem("listwarps-noperm"));
            }
        }
    }


    /**
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin(): Plugin{
        return $this->api->getSimpleWarp();
    }
}
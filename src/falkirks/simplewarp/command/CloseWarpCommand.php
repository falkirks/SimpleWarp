<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\event\WarpCloseEvent;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;

class CloseWarpCommand extends SimpleWarpCommand {
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct($api->executeTranslationItem("closewarp-cmd"), $api->executeTranslationItem("closewarp-desc"), $api->executeTranslationItem("closewarp-usage"));
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
            if ($sender->hasPermission(SimpleWarpPermissions::OPEN_WARP_COMMAND)) {
                if (isset($args[0])) {
                    if (isset($this->api->getWarpManager()[$args[0]])) {
                        /** @var Warp $warp */
                        $warp = $this->api->getWarpManager()[$args[0]];
                        $ev = new WarpCloseEvent($sender, $warp);
                        $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev);
                        if (!$ev->isCancelled()) {
                            $warp->setPublic(false);
                            $sender->sendMessage($this->api->executeTranslationItem("closed-warp-1", $args[0]));
                            $sender->sendMessage($this->api->executeTranslationItem("closed-warp-2", SimpleWarpPermissions::BASE_WARP_PERMISSION . "." . $warp->getName()));
                        }
                        else {
                            $sender->sendMessage($this->api->executeTranslationItem("closewarp-event-cancelled"));
                        }
                    }
                    else {
                        $sender->sendMessage($this->api->executeTranslationItem("warp-doesnt-exist"));
                    }
                }
                else {
                    $sender->sendMessage($this->getUsage());
                    Version::sendVersionMessage($sender);
                }
            }
            else {
                $sender->sendMessage($this->api->executeTranslationItem("closewarp-noperm"));
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
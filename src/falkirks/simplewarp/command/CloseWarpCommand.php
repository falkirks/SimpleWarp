<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\event\WarpCloseEvent;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class CloseWarpCommand extends SimpleWarpCommand {
    private SimpleWarpAPI $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct($api->executeTranslationItem("closewarp-cmd"), $api->executeTranslationItem("closewarp-desc"), $api->executeTranslationItem("closewarp-usage"));
        $this->api = $api;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(parent::execute($sender, $commandLabel, $args)) {
            if ($sender->hasPermission(SimpleWarpPermissions::OPEN_WARP_COMMAND)) {
                if (isset($args[0])) {
                    if (isset($this->api->getWarpManager()[$args[0]])) {
                        /** @var Warp $warp */
                        $warp = $this->api->getWarpManager()[$args[0]];
                        $ev = new WarpCloseEvent($sender, $warp);
                        $ev->call();
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

    public function getOwningPlugin(): Plugin{
        return $this->api->getSimpleWarp();
    }
}
<?php

namespace falkirks\simplewarp\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;

abstract class SimpleWarpCommand extends Command implements PluginOwned {

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if($this->getPlugin()->isDisabled()){
            $sender->sendMessage($this->getPlugin()->getApi()->executeTranslationItem("plugin-disabled"));
            return false;
        }
        return true;
    }
}
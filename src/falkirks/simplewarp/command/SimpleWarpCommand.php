<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\SimpleWarp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\PluginException;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;

abstract class SimpleWarpCommand extends Command implements PluginIdentifiableCommand {

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

    public abstract function getPlugin(): Plugin;
}
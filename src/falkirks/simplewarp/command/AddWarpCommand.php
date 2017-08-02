<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\Destination;
use falkirks\simplewarp\event\WarpAddEvent;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\utils\WeakPosition;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;

class AddWarpCommand extends SimpleWarpCommand {
    private $api;

    public function __construct(SimpleWarpAPI $api) {
        parent::__construct($api->executeTranslationItem("addwarp-cmd"), $api->executeTranslationItem("addwarp-desc"), $api->executeTranslationItem("addwarp-usage"));
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
        if (parent::execute($sender, $commandLabel, $args)) {
            if ($sender->hasPermission(SimpleWarpPermissions::ADD_WARP_COMMAND)) {
                if (isset($args[0])) {
                    if (!isset($this->api->getWarpManager()[$args[0]])) {
                        if (substr($args[0], 0, 4) === "ess:" && $this->api->getConfigItem("essentials-support") && $sender->hasPermission("simplewarp.essentials.notice")) {
                            $sender->sendMessage($this->api->executeTranslationItem("addwarp-ess-prefix-warning"));
                        }
                        if (isset($args[3])) {
                            $level = (isset($args[4]) ? $this->api->getSimpleWarp()->getServer()->getLevelByName($args[4]) : $this->api->getSimpleWarp()->getServer()->getDefaultLevel());
                            if ($level instanceof Level) {
                                $dest = new Destination(new WeakPosition($args[1], $args[2], $args[3], $level->getName()));
                                $warp = new Warp($this->api->getWarpManager(), $args[0], $dest);
                                $ev = new WarpAddEvent($sender, $warp);
                                $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev);
                                if (!$ev->isCancelled()) {
                                    $this->api->getWarpManager()[$args[0]] = $warp;
                                    $sender->sendMessage($this->api->executeTranslationItem("warp-added-xyz", $args[0], $dest->toString()));
                                }
                                else {
                                    $sender->sendMessage($this->api->executeTranslationItem("addwarp-event-cancelled"));
                                }
                            }
                            else {
                                $sender->sendMessage($this->api->executeTranslationItem("level-not-loaded"));
                            }
                        }
                        elseif (isset($args[2])) {
                            $dest = new Destination($args[1], $args[2]);
                            $warp = new Warp($this->api->getWarpManager(), $args[0], $dest);
                            $ev = new WarpAddEvent($sender, $warp);
                            $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev);
                            if (!$ev->isCancelled()) {
                                $this->api->getWarpManager()[$args[0]] = $warp;
                                $sender->sendMessage($this->api->executeTranslationItem("warp-added-server", $args[0], $dest->toString()));
                                if (!$this->api->supportsExternalWarps()) {
                                    $sender->sendMessage($this->api->executeTranslationItem("needs-external-warps"));
                                }
                            }
                            else {
                                $sender->sendMessage($this->api->executeTranslationItem("addwarp-event-cancelled"));
                            }
                        }
                        elseif (isset($args[1])) {
                            if (($player = $this->api->getSimpleWarp()->getServer()->getPlayer($args[1])) instanceof Player) {
                                $dest = new Destination(new Position($player->getX(), $player->getY(), $player->getZ(), $player->getLevel()));
                                $warp = new Warp($this->api->getWarpManager(), $args[0], $dest);
                                $ev = new WarpAddEvent($sender, $warp);
                                $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev);
                                if (!$ev->isCancelled()) {
                                    $this->api->getWarpManager()[$args[0]] = $warp;
                                    $sender->sendMessage($this->api->executeTranslationItem("warp-added-player", $args[0], $dest->toString()));
                                }
                                else {
                                    $sender->sendMessage($this->api->executeTranslationItem("addwarp-event-cancelled"));
                                }
                            }
                            else {
                                $sender->sendMessage($this->api->executeTranslationItem("player-not-loaded"));
                            }
                        }
                        else {
                            if ($sender instanceof Player) {
                                $dest = new Destination(new Position($sender->getX(), $sender->getY(), $sender->getZ(), $sender->getLevel()));
                                $warp = new Warp($this->api->getWarpManager(), $args[0], $dest);
                                $ev = new WarpAddEvent($sender, $warp);
                                $this->getPlugin()->getServer()->getPluginManager()->callEvent($ev);
                                if (!$ev->isCancelled()) {
                                    $this->api->getWarpManager()[$args[0]] = $warp;
                                    $sender->sendMessage($this->api->executeTranslationItem("warp-added-self", $args[0], $dest->toString()));
                                }
                                else {
                                    $sender->sendMessage($this->api->executeTranslationItem("addwarp-event-cancelled"));
                                }
                            }
                            else {
                                $sender->sendMessage($this->getUsage());
                            }
                        }
                    }
                    else {
                        $sender->sendMessage($this->api->executeTranslationItem("bad-warp-name"));
                    }
                }
                else {
                    $sender->sendMessage($this->getUsage());
                    Version::sendVersionMessage($sender);
                }
            }
            else {
                $sender->sendMessage($this->api->executeTranslationItem("addwarp-no-perm"));
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
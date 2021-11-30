<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\task\CommandWarpTask;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\CommandSender;
use pocketmine\world\particle\SmokeParticle;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use pocketmine\plugin\Plugin;

class WarpCommand extends SimpleWarpCommand {
    protected SimpleWarpAPI $api;

    public function __construct(SimpleWarpAPI $api) {
        parent::__construct($api->executeTranslationItem("warp-cmd"), $api->executeTranslationItem("warp-desc"), $api->executeTranslationItem("warp-usage"));
        $this->api = $api;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (parent::execute($sender, $commandLabel, $args)) {
            if ($sender->hasPermission(SimpleWarpPermissions::WARP_COMMAND)) {
                if (isset($args[0])) {
                    if (isset($this->api->getWarpManager()[$args[0]])) {
                        /** @var SimpleWarp $plugin */
                        $plugin = $this->getOwningPlugin();
                        if (isset($args[1])) {
                            if ($sender->hasPermission(SimpleWarpPermissions::WARP_OTHER_COMMAND)) {
                                if (($player = $this->api->getSimpleWarp()->getServer()->getPlayerExact($args[1])) instanceof Player) {
                                    /** @var Warp $warp */
                                    $warp = $this->api->getWarpManager()[$args[0]];
                                    if ($warp->canUse($sender)) {

                                        $task = new CommandWarpTask($plugin, $warp, $player, $sender);
                                        $task->run();
                                    }
                                    else {
                                        $sender->sendMessage($this->api->executeTranslationItem("no-permission-warp"));
                                    }
                                }
                                else {
                                    $sender->sendMessage($this->api->executeTranslationItem("player-not-loaded"));
                                }
                            }
                            else {
                                $sender->sendMessage($this->api->executeTranslationItem("no-permission-warp-other"));
                            }
                        }
                        elseif ($sender instanceof Player) {
                            /** @var Warp $warp */
                            $warp = $this->api->getWarpManager()[$args[0]];
                            if ($warp->canUse($sender)) {
                                $task = new CommandWarpTask($plugin, $warp, $sender, $sender);
                                $task->run();
                            }
                            else {
                                $sender->sendMessage($this->api->executeTranslationItem("no-permission-warp"));
                            }
                        }
                        else {
                            $sender->sendMessage($this->getUsage());

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
                $sender->sendMessage($this->api->executeTranslationItem("warp-noperm"));
            }
        }
    }

    public function displaySmoke(Position $pos) {
        //particle smoke 120 71 124 1 1 1 35 200
        $random = new Random((int)(microtime(true) * 1000) + mt_rand());

        $particle = new SmokeParticle(200);
        for ($i = 0; $i < 35; ++$i) {
            $vec = new Vector3(
                $pos->x + $random->nextSignedFloat(),
                $pos->y + $random->nextSignedFloat(),
                $pos->z + $random->nextSignedFloat()
            );
            $pos->getWorld()->addParticle($vec, $particle);
        }
    }

    public function getOwningPlugin(): Plugin{
        return $this->api->getSimpleWarp();
    }
}
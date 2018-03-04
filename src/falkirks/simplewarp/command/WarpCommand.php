<?php

namespace falkirks\simplewarp\command;

use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\task\CommandWarpTask;
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
use pocketmine\plugin\Plugin;

class WarpCommand extends SimpleWarpCommand {
    protected $api;

    public function __construct(SimpleWarpAPI $api) {
        parent::__construct($api->executeTranslationItem("warp-cmd"), $api->executeTranslationItem("warp-desc"), $api->executeTranslationItem("warp-usage"));
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
            if ($sender->hasPermission(SimpleWarpPermissions::WARP_COMMAND)) {
                if (isset($args[0])) {
                    if (isset($this->api->getWarpManager()[$args[0]])) {
                        if (isset($args[1])) {
                            if ($sender->hasPermission(SimpleWarpPermissions::WARP_OTHER_COMMAND)) {
                                if (($player = $this->api->getSimpleWarp()->getServer()->getPlayer($args[1])) instanceof Player) {
                                    /** @var Warp $warp */
                                    $warp = $this->api->getWarpManager()[$args[0]];
                                    if ($warp->canUse($sender)) {
                                        $task = new CommandWarpTask($this->getPlugin(), $warp, $player, $sender);
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
                                $task = new CommandWarpTask($this->getPlugin(), $warp, $sender, $sender);
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

        $particle = new SmokeParticle(new Vector3($pos->x, $pos->y + 0.7, $pos->z), 200);
        for ($i = 0; $i < 35; ++$i) {
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
    public function getPlugin(): Plugin{
        return $this->api->getSimpleWarp();
    }
}
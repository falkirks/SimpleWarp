<?php
namespace falkirks\simplewarp\api;


use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\Warp;
use falkirks\simplewarp\WarpManager;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

/**
 * This class provides an API for interacting with
 * SimpleWarp, whenever possible, use methods from this
 * class instead of elsewhere.
 *
 * Class SimpleWarpAPI
 * @package falkirks\simplewarp\api
 */
class SimpleWarpAPI {
    private $plugin;
    public function __construct(SimpleWarp $simpleWarp){
        $this->plugin = $simpleWarp;
    }
    public function getSimpleWarp(): SimpleWarp{
        return $this->plugin;
    }
    public function getConfigItem($name): string{
        return $this->plugin->getConfig()->get($name);
    }
    public function executeTranslationItem($name, ...$args): string{
        return $this->plugin->getTranslationManager()->execute($name, ...$args);
    }
    public function getTranslationItem($name): string{
        return $this->plugin->getTranslationManager()->get($name);
    }

    /**
     * @param $name
     * @return Warp
     */
    public function getWarp($name): Warp{
        return $this->getWarpManager()[$name];
    }
    public function addWarp($name, Warp $warp){
        $this->getWarpManager()[$name] = $warp;
    }
    public function warpPlayerTo(Player $player, $name): bool{
        $warp = $this->getWarp($name);
        if($warp instanceof Warp){
            $warp->teleport($player);
            return true;
        }
        return false;
    }
    public function canPlayerUse(Player $player, $name): bool{
        $warp = $this->getWarp($name);
        if($warp instanceof Warp){
            return $warp->canUse($player);
        }
        return null;
    }
    public function getWarpManager(): WarpManager{
        return $this->plugin->getWarpManager();
    }
    public function isFastTransferLoaded(): bool{
        return $this->getSimpleWarp()->getServer()->getPluginManager()->getPlugin("FastTransfer") instanceof PluginBase;
    }
    /**
     * This will hopefully save someone typing.
     * @param PluginBase $base
     * @return SimpleWarpAPI
     */
    public static function getInstance(PluginBase $base): SimpleWarpAPI{
        return $base->getServer()->getPluginManager()->getPlugin("SimpleWarp")->getApi();
    }
}
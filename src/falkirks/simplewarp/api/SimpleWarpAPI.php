<?php
namespace falkirks\simplewarp\api;


use falkirks\simplewarp\lang\TranslationManager;
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
 * This class is highly stable and very unlikely to change.
 *
 * Class SimpleWarpAPI
 * @package falkirks\simplewarp\api
 */
class SimpleWarpAPI {
    /**
     * @var SimpleWarp
     */
    private $plugin;

    /**
     * SimpleWarpAPI constructor.
     * @param SimpleWarp $simpleWarp
     */
    public function __construct(SimpleWarp $simpleWarp){
        $this->plugin = $simpleWarp;
    }

    /**
     * gets a copy of the SimpleWarp plugin object
     * @return SimpleWarp
     */
    public function getSimpleWarp(): SimpleWarp{
        return $this->plugin;
    }

    /**
     * gets a config option
     * example SimpleWarpAPI::getConfigItem('display-exact-coordinates')
     * @param $name
     * @return string
     */
    public function getConfigItem($name): string{
        return $this->plugin->getConfig()->get($name);
    }

    /**
     * @param $name
     * @param array ...$args
     * @return string
     */
    public function executeTranslationItem($name, ...$args): string{
        return $this->plugin->getTranslationManager()->execute($name, ...$args);
    }

    /**
     * @param $name
     * @return string
     */
    public function getTranslationItem($name): string{
        return $this->plugin->getTranslationManager()->get($name);
    }

    /**
     * Gets a warp object with $name from the current WarpManager
     * @param $name
     * @return Warp
     */
    public function getWarp($name): Warp{
        return $this->getWarpManager()[$name];
    }

    /**
     * Adds a new Warp to the WarpManager, will be saved according to storage-mode
     * @param $name
     * @param Warp $warp
     */
    public function addWarp($name, Warp $warp){
        $this->getWarpManager()[$name] = $warp;
    }

    /**
     * Warps a player to a warp with $name without checking
     * if the player can use that warp.
     * @param Player $player
     * @param $name
     * @return bool
     */
    public function warpPlayerTo(Player $player, $name): bool{
        $warp = $this->getWarp($name);
        if($warp instanceof Warp){
            $warp->teleport($player);
            return true;
        }
        return false;
    }

    /**
     * Checks if a player has permission to use a warp
     * @param Player $player
     * @param $name
     * @return bool
     */
    public function canPlayerUse(Player $player, $name): bool{
        $warp = $this->getWarp($name);
        if($warp instanceof Warp){
            return $warp->canUse($player);
        }
        return null;
    }

    /**
     * Returns the TranslationManager
     * @return TranslationManager
     */
    public function getTranslationManager(): TranslationManager{
        return $this->plugin->getTranslationManager();
    }

    /**
     * Sets the TranslationManager
     * ! Will inject your code into SimpleWarp, potentially breaking !
     * @param TranslationManager $translationManager
     */
    public function setTranslationManager(TranslationManager $translationManager){
        $this->plugin->setTranslationManager($translationManager);
    }

    /**
     * Returns the WarpManager
     * @return WarpManager
     */
    public function getWarpManager(): WarpManager{
        return $this->plugin->getWarpManager();
    }

    /**
     * Sets the WarpManager, the old one's data store will be saved before a new one is added
     * ! Will inject your code into SimpleWarp, potentially breaking !
     * @param WarpManager $warpManager
     */
    public function setWarpManager(WarpManager $warpManager){
        $this->plugin->setWarpManager($warpManager);
    }

    /**
     * Produces true if FastTransfer is loaded and enabled
     * DEPRECATED!
     * @deprecated
     * @return bool
     */
    public function isFastTransferLoaded(): bool{
        return $this->getSimpleWarp()->getServer()->getPluginManager()->getPlugin("FastTransfer") instanceof PluginBase;
    }
    /**
     * This will hopefully save someone typing.
     * Call SimpleWarpAPI::getInstance($this) from your main class to get the current API instance
     * @param PluginBase $base
     * @return SimpleWarpAPI
     */
    public static function getInstance(PluginBase $base): SimpleWarpAPI{
        return $base->getServer()->getPluginManager()->getPlugin("SimpleWarp")->getApi();
    }
}
<?php
namespace falkirks\simplewarp\api;


use falkirks\simplewarp\Destination;
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
     * @return Warp|null
     */
    public function getWarp($name){
        return $this->getWarpManager()[$name];
    }

    /**
     * Adds a new Warp to the WarpManager, will be saved according to storage-mode
     * @param Warp $warp
     */
    public function saveWarp(Warp $warp){
        $this->getWarpManager()[$warp->getName()] = $warp;
    }

    /**
     * Creates a new warp object and saves it.
     *
     * @param $name
     * @param Destination $dest
     * @param bool $isPublic
     * @param array $metadata
     * @return Warp
     */
    public function makeWarp($name, Destination $dest, $isPublic = false, $metadata = []){
        $w = new Warp($this->getWarpManager(), $name, $dest, $isPublic, $metadata);
        $this->saveWarp($w);
        return $w;
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
     * gets whether warp metadata is being saved
     * you can use metadata regardless, it just won't be saved on restart
     * @return string
     */
    public function isMetadataSaved(){
        return true;
    }

    /**
     * Gets the value of a metadata item on a warp safely
     * IMPORANT: All plugin metadata keys should be expressed as "namespace-keyname"
     *           this will avoid potential collisions.
     *           You can use the provided getSafeMetadataName for this.
     * @param Warp $warp
     * @param $key
     * @return mixed
     */
    public function getMetadata(Warp $warp, $key){
        return $warp->getMetadata($key);
    }

    /**
     * Sets the value of a metadata item on a warp safely
     * IMPORANT: All plugin metadata keys should be expressed as "namespace-keyname"
     *           this will avoid potential collisions.
     *           You can use the provided getSafeMetadataName for this.
     * @param Warp $warp
     * @param $key
     * @param $value
     */
    public function setMetadata(Warp $warp, $key, $value){
        $warp->setMetadata($key, $value);
    }

    /**
     * Gets warp(s) with the key value pair of metadata as specified
     *
     * IMPORANT: All plugin metadata keys should be expressed as "namespace-keyname"
     *           this will avoid potential collisions.
     *           You can use the provided getSafeMetadataName for this.
     * @param $key
     * @param $value
     * @return array
     */
    public function getWarpsFromMetadata($key, $value): array{
        $ret = [];
        foreach ($this->getWarpManager() as $warp){
            if($warp instanceof Warp && $warp->getMetadata($key) === $value){
                $ret[] = $warp;
            }
        }
        return $ret;
    }

    /**
     * gets a c
     * @param PluginBase $plugin
     * @param $key
     * @return string
     */
    public function getSafeMetadataName(PluginBase $plugin, $key){
        return $plugin->getName() . "-" . $key;
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
     * Returns true if external warps are supported, false otherwise
     * @return bool
     */
    public function supportsExternalWarps(): bool {
        return method_exists(Player::class, "transfer") || $this->getSimpleWarp()->getServer()->getPluginManager()->getPlugin("FastTransfer") instanceof PluginBase;
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

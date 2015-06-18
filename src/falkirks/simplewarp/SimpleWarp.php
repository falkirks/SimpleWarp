<?php
namespace falkirks\simplewarp;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\command\AddWarpCommand;
use falkirks\simplewarp\command\CloseWarpCommand;
use falkirks\simplewarp\command\DelWarpCommand;
use falkirks\simplewarp\command\ListWarpsCommand;
use falkirks\simplewarp\command\OpenWarpCommand;
use falkirks\simplewarp\command\WarpCommand;
use falkirks\simplewarp\lang\TranslationManager;
use falkirks\simplewarp\store\YAMLStore;
use falkirks\simplewarp\utils\WeakPosition;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class SimpleWarp extends PluginBase{
    /** @var  SimpleWarpAPI */
    private $api;
    /** @var  WarpManager */
    private $warpManager;
    /** @var  TranslationManager */
    private $translationManager;

    public function onEnable(){
        $this->saveDefaultConfig();

        $this->api = new SimpleWarpAPI($this);
        $this->translationManager = new TranslationManager($this->api, new YAMLStore(new Config($this->getDataFolder() . "lang.yml", Config::YAML)));
        $this->warpManager = new WarpManager($this->api, new YAMLStore(new Config($this->getDataFolder() . "warps.yml", Config::YAML)), ($this->getConfig()->get('storage-mode') != null ? $this->getConfig()->get('storage-mode') : WarpManager::MEMORY_TILL_CLOSE));
        $this->getServer()->getCommandMap()->registerAll("simplewarp", [
            new WarpCommand($this->api),
            new AddWarpCommand($this->api),
            new DelWarpCommand($this->api),
            new ListWarpsCommand($this->api),
            new OpenWarpCommand($this->api),
            new CloseWarpCommand($this->api)
        ]);
        $pos = new WeakPosition(0, 0, 0, "world");
        $pos->level;

    }
    public function onDisable(){
        $this->warpManager->saveAll();
    }

    /**
     * @return WarpManager
     */
    public function getWarpManager(){
        return $this->warpManager;
    }

    /**
     * @return TranslationManager
     */
    public function getTranslationManager(){
        return $this->translationManager;
    }

    /**
     * @return mixed
     */
    public function getApi(){
        return $this->api;
    }

}
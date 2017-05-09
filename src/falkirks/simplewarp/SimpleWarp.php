<?php
namespace falkirks\simplewarp;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\command\AddWarpCommand;
use falkirks\simplewarp\command\CloseWarpCommand;
use falkirks\simplewarp\command\DelWarpCommand;
use falkirks\simplewarp\command\essentials\EssentialsDelWarpCommand;
use falkirks\simplewarp\command\essentials\EssentialsWarpCommand;
use falkirks\simplewarp\command\ListWarpsCommand;
use falkirks\simplewarp\command\OpenWarpCommand;
use falkirks\simplewarp\command\WarpCommand;
use falkirks\simplewarp\command\WarpReportCommand;
use falkirks\simplewarp\lang\TranslationManager;
use falkirks\simplewarp\store\YAMLStore;
use falkirks\simplewarp\utils\DebugDumpFactory;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class SimpleWarp extends PluginBase{
    /** @var  SimpleWarpAPI */
    private $api;
    /** @var  WarpManager */
    private $warpManager;
    /** @var  TranslationManager */
    private $translationManager;
    /** @var  DebugDumpFactory */
    private $debugDumpFactory;

    public function onEnable(){
        $this->saveDefaultConfig();

        $this->api = new SimpleWarpAPI($this);
        $this->debugDumpFactory = new DebugDumpFactory($this->api);
        $this->translationManager = new TranslationManager($this->api, new YAMLStore(new Config($this->getDataFolder() . "lang.yml", Config::YAML)));
        $this->warpManager = new WarpManager($this->api, new YAMLStore(new Config($this->getDataFolder() . "warps.yml", Config::YAML)), ($this->getConfig()->get('storage-mode') != null ? $this->getConfig()->get('storage-mode') : WarpManager::MEMORY_TILL_CLOSE));
        if($this->getServer()->getPluginManager()->getPlugin("EssentialsPE") instanceof Plugin && $this->getConfig()->get("essentials-support")){
            $this->getLogger()->info("Enabling EssentialsPE support...");
            $warpCommand = $this->getServer()->getCommandMap()->getCommand("warp");
            $delWarpCommand = $this->getServer()->getCommandMap()->getCommand("delwarp");

            $this->unregisterCommands([
                "warp",
                "delwarp"
            ]);

            $this->getServer()->getCommandMap()->registerAll("simplewarp", [
                new EssentialsWarpCommand($this->api, $warpCommand),
                new AddWarpCommand($this->api),
                new EssentialsDelWarpCommand($this->api, $delWarpCommand),
                new ListWarpsCommand($this->api),
                new OpenWarpCommand($this->api),
                new CloseWarpCommand($this->api)
            ]);


        }
        else {
            $this->getServer()->getCommandMap()->registerAll("simplewarp", [
                new WarpCommand($this->api),
                new AddWarpCommand($this->api),
                new DelWarpCommand($this->api),
                new ListWarpsCommand($this->api),
                new OpenWarpCommand($this->api),
                new CloseWarpCommand($this->api),
                new WarpReportCommand($this->api)
            ]);
        }
        if(file_exists($this->getDataFolder() . ".started") && $this->warpManager->getFlag() === WarpManager::MEMORY_TILL_CLOSE){
            $this->getLogger()->critical("SimpleWarp is starting in an inconsistent state. This is likely due to a server crash. You are using storage-mode=0 which means you could have lost data. Read more at http://bit.ly/0data");
        }
        file_put_contents($this->getDataFolder() . ".started", "true");
    }
    public function onDisable(){
        $this->warpManager->saveAll();
        @unlink($this->getDataFolder() . ".started");
        if(file_exists($this->getDataFolder() . ".started")){
            $this->getLogger()->alert("Unable to clean up session file. You will be shown an error next time you start. You can ignore it.");
        }
    }

    /**
     * @return DebugDumpFactory
     */
    public function getDebugDumpFactory(): DebugDumpFactory{
        return $this->debugDumpFactory;
    }



    /**
     * @return WarpManager
     */
    public function getWarpManager(): WarpManager{
        return $this->warpManager;
    }

    /**
     * @return TranslationManager
     */
    public function getTranslationManager(): TranslationManager{
        return $this->translationManager;
    }

    /**
     * @param WarpManager $warpManager
     */
    public function setWarpManager(WarpManager $warpManager){
        $warpManager->saveAll();
        $this->warpManager = $warpManager;
    }

    /**
     * @param TranslationManager $translationManager
     */
    public function setTranslationManager(TranslationManager $translationManager){
        $this->translationManager = $translationManager;
    }

    /**
     * @return SimpleWarpAPI
     */
    public function getApi(): SimpleWarpAPI{
        return $this->api;
    }

    /**
     * Function to easily disable commands
     *
     * @param array $commands
     */
    private function unregisterCommands(array $commands){
        $commandMap = $this->getServer()->getCommandMap();
        foreach($commands as $label){
            $command = $commandMap->getCommand($label);
            $command->setLabel($label . "_disabled");
            $command->unregister($commandMap);
        }
    }

}
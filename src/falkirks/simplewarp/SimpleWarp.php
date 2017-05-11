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
use falkirks\simplewarp\utils\ChecksumVerify;
use falkirks\simplewarp\utils\DebugDumpFactory;
use falkirks\simplewarp\utils\SpoonDetector;
use pocketmine\command\Command;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class SimpleWarp extends PluginBase{
    /** @var  SimpleWarpAPI */
    private $api;
    /** @var  WarpManager */
    private $warpManager;
    /** @var  TranslationManager */
    private $translationManager;
    /** @var  DebugDumpFactory */
    private $debugDumpFactory;
    /** @var  Command[] */
    private $commands;

    public function onEnable(){
        $this->saveDefaultConfig();

        $this->api = new SimpleWarpAPI($this);
        $this->debugDumpFactory = new DebugDumpFactory($this->api);
        $this->translationManager = new TranslationManager($this->api, new YAMLStore(new Config($this->getDataFolder() . "lang.yml", Config::YAML)));
        $this->warpManager = new WarpManager($this->api, new YAMLStore(new Config($this->getDataFolder() . "warps.yml", Config::YAML)), ($this->getConfig()->get('storage-mode') != null ? $this->getConfig()->get('storage-mode') : WarpManager::MEMORY_TILL_CLOSE));

        $this->commands = [
            new ListWarpsCommand($this->api),
            new OpenWarpCommand($this->api),
            new CloseWarpCommand($this->api),
            new WarpReportCommand($this->api),
            new AddWarpCommand($this->api)
        ];
        if($this->getServer()->getPluginManager()->getPlugin("EssentialsPE") instanceof Plugin && $this->getConfig()->get("essentials-support")){
            $this->getLogger()->info("Enabling EssentialsPE support...");
            $warpCommand = $this->getServer()->getCommandMap()->getCommand("warp");
            $delWarpCommand = $this->getServer()->getCommandMap()->getCommand("delwarp");
            $this->unregisterCommands([
                "warp",
                "delwarp"
            ]);
            array_push($this->commands, new EssentialsWarpCommand($this->api, $warpCommand));
            array_push($this->commands, new EssentialsDelWarpCommand($this->api, $delWarpCommand));
        }
        else {
            array_push($this->commands, new WarpCommand($this->api));
            array_push($this->commands, new DelWarpCommand($this->api));
        }

        $this->getServer()->getCommandMap()->registerAll("simplewarp", $this->commands);

        if(file_exists($this->getDataFolder() . ".started") && $this->warpManager->getFlag() === WarpManager::MEMORY_TILL_CLOSE){
            $this->getLogger()->critical("SimpleWarp is starting in an inconsistent state. This is likely due to a server crash. You are using storage-mode=0 which means you could have lost data. Read more at http://bit.ly/0data");
        }

        file_put_contents($this->getDataFolder() . ".started", "true");
        SpoonDetector::printSpoon($this, 'spoon.txt');

        if(ChecksumVerify::isValid($this)){
            $this->getLogger()->info(TextFormat::LIGHT_PURPLE . "Your copy of SimpleWarp was verified." . TextFormat::RESET);
        }
        else{
            //TODO add negative response (in next version because I don't know if this works)
        }
    }
    public function onDisable(){
        $this->warpManager->saveAll();

        $this->warpManager = null;
        $this->api = null;
        $this->debugDumpFactory = null;
        $this->translationManager = null;

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

    /**
     * @return Command[]
     */
    public function getCommands(): array {
        return $this->commands;
    }
}
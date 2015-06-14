<?php
namespace falkirks\simplewarp\command;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;

class OpenWarpCommand extends Command implements PluginIdentifiableCommand{
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct($api->executeTranslationItem("openwarp-cmd"), $api->executeTranslationItem("openwarp-desc"), $api->executeTranslationItem("openwarp-usage"));
        $this->api = $api;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, $commandLabel, array $args){
        if($sender->hasPermission(SimpleWarpPermissions::OPEN_WARP_COMMAND)){
            if(isset($args[0])){
                if(isset($this->api->getWarpManager()[$args[0]])) {
                    /** @var Warp $warp */
                    $warp = $this->api->getWarpManager()[$args[0]];
                    $warp->setPublic(true);
                    $this->api->getWarpManager()[$args[0]] = $warp;
                    $sender->sendMessage($this->api->executeTranslationItem("opened-warp-1", $args[0]));
                    $sender->sendMessage($this->api->executeTranslationItem("opened-warp-2"));
                }
                else{
                    $sender->sendMessage($this->api->executeTranslationItem("warp-doesnt-exist", $args[0]));
                }
            }
            else{
                $sender->sendMessage($this->getUsage());
                Version::sendVersionMessage($sender);
            }
        }
        else{
            $sender->sendMessage($this->api->executeTranslationItem("openwarp-noperm"));
        }
    }


    /**
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin(){
        return $this->api->getSimpleWarp();
    }
}
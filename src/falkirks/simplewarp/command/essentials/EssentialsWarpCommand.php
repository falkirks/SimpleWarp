<?php
namespace falkirks\simplewarp\command\essentials;

use EssentialsPE\Loader;
use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\command\WarpCommand;
use falkirks\simplewarp\SimpleWarp;
use falkirks\simplewarp\Version;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class EssentialsWarpCommand extends WarpCommand{
    /** @var Command  */
    private $essCommand;
    public function __construct(SimpleWarpAPI $api, Command $essCommand){
        parent::__construct($api);
        $this->essCommand = $essCommand;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(isset($args[0])) {
            $ess = $this->getEssAPI();
            if (isset($this->api->getWarpManager()[$args[0]])) {
                parent::execute($sender, $commandLabel, $args);
                if($ess->warpExists($args[0]) && $sender->hasPermission("simplewarp.essentials.notice")){
                    $sender->sendMessage($this->api->executeTranslationItem("ess-warp-conflict", $args[0]));
                }
            }
            elseif(($name = $this->getEssWarpName($ess, $args[0])) !== null){
                $args[0] = $name;
                $this->getEssCommand()->execute($sender, $commandLabel, $args);
            }
            else{
                $sender->sendMessage($this->api->executeTranslationItem("ess-warp-doesnt-exist"));
            }
        }
        else{
            $sender->sendMessage($this->getUsage());
            Version::sendVersionMessage($sender);
        }
    }
    private function getEssWarpName($loader, $string){
        if($loader->warpExists($string)){
            return $string;
        }
        if(substr($string, 0, 4) === "ess:" && $loader->warpExists(substr($string, 4))){
            return substr($string, 4);
        }
        return null;
    }
    private function getEssAPI(){
        $ess = $this->getPlugin()->getServer()->getPluginManager()->getPlugin("EssentialsPE");
        if(method_exists($ess, "getAPI")){
            return $ess->getAPI();
        }
        return $ess;
    }
    /**
     * @return Command
     */
    public function getEssCommand(): SimpleWarp{
        return $this->essCommand;
    }
}

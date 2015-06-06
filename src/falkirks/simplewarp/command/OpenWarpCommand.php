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
        parent::__construct("openwarp", "Open existing warps.", "/openwarp <name>");
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
                    $sender->sendMessage("You have opened a warp called " . TextFormat::AQUA . $args[0] . TextFormat::RESET);
                    $sender->sendMessage("  Any player will be able to use this warp.");
                }
                else{
                    $sender->sendMessage(TextFormat::RED . "That warp doesn't exist." . TextFormat::RESET);
                }
            }
            else{
                $sender->sendMessage($this->getUsage());
                Version::sendVersionMessage($sender);
            }
        }
        else{
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command" . TextFormat::RESET);
        }
    }


    /**
     * @return \pocketmine\plugin\Plugin
     */
    public function getPlugin(){
        return $this->api->getSimpleWarp();
    }
}
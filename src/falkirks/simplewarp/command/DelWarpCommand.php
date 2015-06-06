<?php
namespace falkirks\simplewarp\command;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\Version;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;

class DelWarpCommand extends Command implements PluginIdentifiableCommand{
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct("delwarp", "Delete existing warps.", "/delwarp <name>");
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
        if($sender->hasPermission(SimpleWarpPermissions::DEL_WARP_COMMAND)){
            $sender->sendMessage("THIS IS DELWARP");
            if(isset($args[0])){
                if(isset($this->api->getWarpManager()[$args[0]])) {
                    unset($this->api->getWarpManager()[$args[0]]);
                    $sender->sendMessage("You have deleted a warp called " . TextFormat::AQUA . $args[0] . TextFormat::RESET);
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
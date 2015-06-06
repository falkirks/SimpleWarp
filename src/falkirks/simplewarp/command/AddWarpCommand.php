<?php
namespace falkirks\simplewarp\command;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\Destination;
use falkirks\simplewarp\permission\SimpleWarpPermissions;
use falkirks\simplewarp\Version;
use falkirks\simplewarp\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class AddWarpCommand extends Command implements PluginIdentifiableCommand{
    private $api;
    public function __construct(SimpleWarpAPI $api){
        parent::__construct("addwarp", "Add new warps.", "/addwarp <name> [<ip> <port>|<x> <y> <z> <level>|<player>]");
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
        if($sender->hasPermission(SimpleWarpPermissions::ADD_WARP_COMMAND)){
            if(isset($args[0])) {
                if(!isset($this->api->getWarpManager()[$args[0]])) {
                    if (isset($args[4])) {
                        $level = $this->api->getSimpleWarp()->getServer()->getLevelByName($args[4]);
                        if ($level instanceof Level) {
                            $dest = new Destination(new Position($args[1], $args[2], $args[3], $level));
                            $warp = new Warp($args[0], $dest);
                            $this->api->getWarpManager()[$args[0]] = $warp;
                            $sender->sendMessage("You have created a warp called " . TextFormat::AQUA . $args[0] . TextFormat::RESET . " " . $dest->toString());
                        }
                        else {
                            $sender->sendMessage(TextFormat::RED . "You specified a level which isn't loaded." . TextFormat::RESET);
                        }
                    }
                    elseif (isset($args[2])) {
                        $dest = new Destination($args[1], $args[2]);
                        $warp = new Warp($args[0], $dest);
                        $this->api->getWarpManager()[$args[0]] = $warp;
                        $sender->sendMessage("You have created a warp called " . TextFormat::AQUA . $args[0] . TextFormat::RESET . " " . $dest->toString());
                        if (!$this->api->isFastTransferLoaded()) {
                            $sender->sendMessage("This warp needs " . TextFormat::AQUA . "FastTransfer" . TextFormat::RESET . ", you will need to install it to use this warp.");
                        }
                    }
                    elseif (isset($args[1])) {
                        if (($player = $this->api->getSimpleWarp()->getServer()->getPlayer($args[1])) instanceof Player) {
                            $dest = new Destination(new Position($player->getX(), $player->getY(), $player->getZ(), $player->getLevel()));
                            $warp = new Warp($args[0], $dest);
                            $this->api->getWarpManager()[$args[0]] = $warp;
                            $sender->sendMessage("You have created a warp called " . TextFormat::AQUA . $args[0] . TextFormat::RESET . " " . $dest->toString());
                        }
                        else {
                            $sender->sendMessage(TextFormat::RED . "You specified a player which isn't loaded." . TextFormat::RESET);
                        }
                    }
                    else {
                        if ($sender instanceof Player) {
                            $dest = new Destination(new Position($sender->getX(), $sender->getY(), $sender->getZ(), $sender->getLevel()));
                            $warp = new Warp($args[0], $dest);
                            $this->api->getWarpManager()[$args[0]] = $warp;
                            $sender->sendMessage("You have created a warp called " . TextFormat::AQUA . $args[0] . TextFormat::RESET . " " . $dest->toString());
                        }
                        else {
                            $sender->sendMessage($this->getUsage());
                        }
                    }
                }
                else{
                    $sender->sendMessage(TextFormat::RED . "That warp name is invalid." . TextFormat::RESET);
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
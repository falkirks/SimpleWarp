<?php
namespace simplewarp;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\permission\Permission;


class SimpleWarp extends PluginBase implements CommandExecutor, Listener {
    /** @var  Permission */
    public $perm;
    /** @var  Warp[] */
    public $warps;
    /** @var Config */
    public $warpConfig;
    /** @var  array */
    private $publicList;

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->warpConfig = new Config($this->getDataFolder()."warps.yml", Config::YAML, array());
        $this->publicList = (new Config($this->getDataFolder() . "public.txt", Config::ENUM))->getAll();
        var_dump($this->publicList);
        $this->perm = $this->getServer()->getPluginManager()->getPermission("simplewarp.warp");
        $this->warps = $this->parseWarps($this->warpConfig->getAll());
    }
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        switch($cmd->getName()) {
            case "warp":
                if (isset($args[0])){
                    if (isset($this->warps[$args[0]])){
                        if(isset($args[1])){
                            if($sender->hasPermission("simplewarp.other")){
                                $p = $this->getServer()->getPlayer($args[1]);
                                if($p !== null && $p->isOnline()){
                                    $this->warps[$args[0]]->warpAs($sender, $p);
                                    return true;
                                }
                                else{
                                    $sender->sendMessage("Couldn't find player.");
                                    return true;
                                }
                            }
                            else{
                                $sender->sendMessage("You don't have permission to warp others.");
                                return true;
                            }
                        }
                        elseif($sender instanceof Player){
                            $this->warps[$args[0]]->warp($sender);
                            return true;
                        }
                        else{
                            $sender->sendMessage("You must specify a player to warp.");
                            return true;
                        }
                    }
                    else{
                        $sender->sendMessage("Warp not found.");
                        return true;
                    }
                }
                else{
                    return $this->listWarpsTo($sender);
                }
                break;
            case 'listwarps':
                return $this->listWarpsTo($sender);
                break;
            case "addwarp":
                if ($sender instanceof Player) {
                    if(isset($args[0])){
                        $this->warpConfig->set($args[0], [$sender->getFloorX(), $sender->getFloorY(), (int) $sender->getFloorZ(), $sender->getLevel()->getName()]);
                        $this->warpConfig->save();
                        $this->warps = $this->parseWarps($this->warpConfig->getAll());
                        $sender->sendMessage("New warp created, " . $args[0]);
                        return true;
                    }
                }
                else {
                    $sender->sendMessage("Please run command in game.");
                    return true;
                }
                break;
            case "delwarp":
                if (isset($args[0])) {
                    if(isset($this->warps[$args[0]])){
                        $this->getServer()->getPluginManager()->removePermission($this->getServer()->getPluginManager()->getPermission("simplewarp.warp." . $args[0]));
                        $this->warpConfig->remove($args[0]);
                        $this->warpConfig->save();
                        unset($this->warps[$args[0]]);
                        $sender->sendMessage($args[0] . " has been removed.");
                        return true;
                    }
                    else{
                        $sender->sendMessage($args[0] . " does not exist.");
                        return true;
                    }
                }
                break;
            default:
                return false;
                break;
        }
    }
    public function listWarpsTo(CommandSender $sender){
        if($sender->hasPermission("simplewarp.list")){
            $ret = "Warp list:\n";
            foreach($this->warps as $w){
                if($w->canUse($sender)){
                    $ret .= " -" . $w->name . "\n";
                }
            }
            $sender->sendMessage(($ret !== "Warp list:\n" ? $ret : "No warps found."));
            return true;
        }
        else{
            return false;
        }
    }
    public function parseWarps(array $w) {
        $ret = [];
        foreach ($w as $n => $data) {
            $this->getServer()->loadLevel($data[3]);
            if(($level = $this->getServer()->getLevelByName($data[3])) === null) $this->getLogger()->error($data[3] . " is not loaded. Warp " . $n . " is disabled.");
            else{
                $ret[$n] = new Warp(new Position($data[0], $data[1], $data[2], $level), $n, isset($this->publicList[$n]));
                $this->warpPermission($ret[$n]);
            }
        }
        return $ret;
    }
    public function warpPermission(Warp $w){
        $p = new Permission("simplewarp.warp." . $w->name,"Allow use of " . $w->name, isset($this->publicList[$w->getName()]));
        $this->perm->getChildren()[$p->getName()] = true;
        $this->getServer()->getPluginManager()->addPermission($p);
    }
}

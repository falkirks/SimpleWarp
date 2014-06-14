<?php
namespace SimpleWarp;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\level\position;
use pocketmine\level\Level;
use pocketmine\permission\Permission;

use SimpleWarp\warp;

class SimpleWarp extends PluginBase implements CommandExecutor, Listener {
  public function onEnable() {
    @mkdir($this->getDataFolder());
    $this->config = new Config($this->getDataFolder()."warps.yml", Config::YAML, array());
    $this->perm = $this->getServer()->getPluginManager()->getPermission("simplewarp.warp"); 
    $this->warps = $this->parseWarps($this->config->getAll()); 
    $this->getLogger()->info("SimpleWarp loaded!\n");
  }

  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
    switch($cmd->getName()) {
      case "warp":
      	if ($sender instanceof Player) {
          if (isset($args[0])){
  				  if (isset($this->warps[$args[0]])) $this->warps[$args[0]]->warp($sender);
  				  else $sender->sendMessage("Warp does not exist!");
            return true;
			   }
		  }
      else {
        $sender->sendMessage("Please run command in game.");
        return true;
      }
      break;
    case "addwarp":
      if ($sender instanceof Player) {
        if(isset($args[0])){
          $this->config->set($args[0], array((int) $sender->x, (int) $sender->y, (int) $sender->z, $sender->getLevel()->getName()));
          $this->config->save();
          $this->warps = $this->parseWarps($this->config->getAll());
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
          $this->config->remove($args[0]);
          $this->config->save();
          unset($this->warps[$args[0]]);
          $sender->sendMessage($args[0] . " has been remopved.");
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
  public function onDisable() {    
    $this->getLogger()->info("SimpleWarp unloading...");
  }
  public function parseWarps($w) {
    $ret = array();
    foreach ($w as $n => $data) {
      if(($level = $this->fetchLevel($data[3])) == false) $this->getLogger()->error($data[3] . " is not loaded. Warp " . $n . " is disabled.");
      else{
        $ret[$n] = new Warp(new Position($data[0], $data[1], $data[2], $level), $n);
        $this->warpPermission($ret[$n]);
      }
    }
    return $ret;
  }
  public function fetchLevel($name){
    foreach ($this->getServer()->getLevels() as $n => $lev) {
      if ($name === $lev->getName()) {
        return $lev;
      }
    }
    return false;
  }
  public function warpPermission(Warp $w){
    $p = new Permission("simplewarp.warp." . $w->name,"Allow use of " . $w->name);
    $this->perm->getChildren()[$p->getName()] = true;
    $this->getServer()->getPluginManager()->addPermission($p);
  }
}

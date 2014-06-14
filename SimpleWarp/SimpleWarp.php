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

use SimpleWarp\warp;

class SimpleWarp extends PluginBase implements CommandExecutor, Listener {
  public function onEnable() {
    @mkdir($this->getDataFolder());
    $this->warps = $this->parseWarps((new Config($this->getDataFolder()."warps.yml", Config::YAML, array()))->getAll());
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getLogger()->info("SimpleWarp loaded!\n");
  }

  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
    switch($cmd->getName()) {
      case "warp":
      	if ($sender instanceof Player) {
          if (isset($args[0])){
  				  if (isset($this->warps[$params[0]])) $this->warps[$params[0]]->warp($sender);
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
        if(isset($arags[0])){
          $yml = $this->api->plugin->readYAML($this->api->plugin->configPath($this). "warps.yml");
          $yml[$args[0]] = array($sender->x, $sender->y, $sender->z, $sender->getLevel()->getName());
          $this->api->plugin->writeYAML($this->api->plugin->configPath($this)."warps.yml", $yml);
          $this->warps = $this->parseWarps($yml);
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
      $ret[$n] = new Warp(new Position($data[0], $data[1], $data[2], $this->getServer()->getLevel($data[3])), $n);
    }
    return $ret;
  }
}

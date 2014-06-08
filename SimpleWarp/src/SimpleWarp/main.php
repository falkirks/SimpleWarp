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

class SimpleWarp extends PluginBase implements CommandExecutor, Listener{
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->warps = $this->parseWarps(new Config($this->getDataFolder()."warps.yml", Config::YAML, array()))->getAll());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->log(TextFormat::GREEN . "[INFO] SimpleWarp loaded!");
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		switch($cmd->getName()){
			case "warp":

				break;
			case "addwarp":

				break;
			case "delwarp":

				break;
			default:
				return false;
			break;
		}
	}
	public function onDisable(){		
		$this->getLogger()->log("SimpleWarp unloading...");
	}
	public function parseWarps($w){
		$ret = array();
		foreach ($w as $n => $data) {
			$ret[$n] = new Warp(new Position($data[0],$data[1],$data[2]));
		}
		return $ret;
	}
}

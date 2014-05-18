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
		$this->config = (new Config($this->getDataFolder()."warps.yml", Config::YAML, array()))->getAll();

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		console(TextFormat::GREEN . "[INFO] SimpleWarp loaded!");
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
		console("SimpleWarp unloading...");
	}
}

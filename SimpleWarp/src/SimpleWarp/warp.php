<?php
namespace SimpleWarp;
use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;

class Warp{
	public $p;
	public $name;
	public function __construct(Position $pos, $n){
		$this->p = $pos;
		$this->name = $n;
	}
	public function warp(CommandSender $s){
		if($s->hasPermission("simplewarp.all") || $s->isPermissionSet("simplewarp.warp." . $this->name)){
			$s->teleport($this->p);
			$s->sendMessage("You just teleported to " . $this->name);
		}
		else{
			$s->sendMessage("You don't have permission to use this warp.");
		}
	}
}

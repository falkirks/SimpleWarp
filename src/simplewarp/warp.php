<?php
namespace simplewarp;

use pocketmine\level\Position;
use pocketmine\command\CommandSender;

class Warp{
	public $p;
	public $name;
	public function __construct(Position $pos, $n){
		$this->p = $pos;
		$this->name = $n;
	}
	public function warp(CommandSender $s){
		if($s->hasPermission("simplewarp.warp") || $s->hasPermission("simplewarp.warp.".$this->name)){
			$s->teleport($this->p);
			$s->sendMessage("You have been teleported to " . $this->name);
		}
		else{
			$s->sendMessage("You don't have permission to use this warp.");
		}
	}
}

<?php
namespace SimpleWarp;
use pocketmine\Player;
use pocketmine\level\position;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;

class Warp{
	public $p;
	public $name;
	public $flags;
	public function __construct(Position $pos, $n, $data){
		$this->p = $pos;
		$this->name = $n;
	}

	public function warp(CommandSender $s){
		if($s->isPermissionSet("simplewarp.all") || $s->isPermissionSet("simplewarp.warp." . $this->name)){
			//Teleport
		}
		else{
			$s->sendMessage("You don't have permission to use this warp.")
		}
	}
}

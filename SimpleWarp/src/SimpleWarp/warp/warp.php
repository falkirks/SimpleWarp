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
	public function __construct(Position $pos, $name){
		$this->p = $pos;

	}

	public function warp(CommandSender $s){

	}
}

<?php
namespace simplewarp;

use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class Warp{
	public $p;
	public $name;
	public function __construct(Position $pos, $n){
		$this->p = $pos;
		$this->name = $n;
	}
	public function warp(CommandSender $p){
		if($this->canUse($p) && $p instanceof Player){
			$p->teleport($this->p);
			$p->sendMessage("You have been teleported to " . $this->name);
		}
        else{
            $p->sendMessage("You don't have permission to use this warp.");
        }
	}
    public function warpAs(CommandSender $sender, Player $p){
        if($this->canUse($sender)){
            $p->teleport($this->p);
            $p->sendMessage("You have been teleported to " . $this->name);
            $sender->sendMessage("Player warped to " . $this->name);
        }
        else{
            $sender->sendMessage("You don't have permission to use this warp.");
        }
    }
    public function canUse(CommandSender $player){
        return ($player->hasPermission("simplewarp.warp") || $player->hasPermission("simplewarp.warp.".$this->name));
    }
}

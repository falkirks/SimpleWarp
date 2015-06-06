<?php
namespace falkirks\simplewarp;


use pocketmine\command\CommandSender;

class Version {
    const ENABLE_VERBOSE = true;
    const VERSION_STRING = "v2.0.0 <-- It's a big one";
    public static function sendVersionMessage(CommandSender $sender){
        if(self::ENABLE_VERBOSE) {
            $sender->sendMessage("Hey, " . $sender->getName());
            $sender->sendMessage("SimpleWarp is a");
        }
        else{
            $sender->sendMessage("SimpleWarp " . self::VERSION_STRING);
        }
    }
}
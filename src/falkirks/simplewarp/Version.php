<?php
namespace falkirks\simplewarp;


use pocketmine\command\CommandSender;

class Version {
    const ENABLE_VERBOSE = false;
    const VERSION_STRING = "v3.3.4";
    private static $quotes = [
        "\"Still round the corner there may wait\nA new road or a secret gate\"\n- J.R.R. Tolkien",
        "\"But here, upon this bank and shoal of time,\n We’d jump the life to come.\"\n - William Shakespeare"
    ];

    public static function sendVersionMessage(CommandSender $sender){
        if(self::ENABLE_VERBOSE) {
            //$sender->sendMessage(Version::$quotes[array_rand(Version::$quotes)]);
        }
        //$sender->sendMessage("SimpleWarp " . self::VERSION_STRING);
    }
}

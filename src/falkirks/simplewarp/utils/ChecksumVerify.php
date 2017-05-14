<?php
namespace falkirks\simplewarp\utils;


use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Utils;

class ChecksumVerify {
    const POGGIT_ENDPOINT = "https://poggit.pmmp.io/get.sha1/";

    /**
     * WARNING! This is a blocking function that performs a web request.
     */
    public static function isValid(PluginBase $pluginBase){
        $url = ChecksumVerify::POGGIT_ENDPOINT . $pluginBase->getDescription()->getName() . "/" . $pluginBase->getDescription()->getVersion();
        $hash = Utils::getURL($url);
        if($pluginBase->getPluginLoader() instanceof PharPluginLoader){
            $reflect = new \ReflectionClass($pluginBase);
            $method = $reflect->getMethod("getFile");
            $method->setAccessible(true);
            $file = $method->invoke($pluginBase);
            $method->setAccessible(false);

            $check = sha1_file(substr($file, 7, -1));
            return $check === $hash;
        }
        return false;

    }

}
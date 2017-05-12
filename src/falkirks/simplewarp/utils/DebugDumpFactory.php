<?php

namespace falkirks\simplewarp\utils;


use falkirks\simplewarp\api\SimpleWarpAPI;

class DebugDumpFactory{
    /** @var  SimpleWarpAPI */
    private $api;


    /**
     * DebugDump constructor.
     */
    public function __construct(SimpleWarpAPI $simpleWarp){
        $this->api = $simpleWarp;
    }


    public function generate(): string {
        return implode("\n", [
            "SERVER VERSION: " . $this->api->getSimpleWarp()->getServer()->getPocketMineVersion(),
            "API: " . $this->api->getSimpleWarp()->getServer()->getApiVersion(),
            "MCPE VERSION: " . $this->api->getSimpleWarp()->getServer()->getVersion(),
            "SOFTWARE: " . $this->api->getSimpleWarp()->getServer()->getName(),
            "SimpleWarp Version: " . $this->api->getSimpleWarp()->getDescription()->getVersion(),
            "PLUGINS: " . implode(",", array_keys($this->api->getSimpleWarp()->getServer()->getPluginManager()->getPlugins())),
            "storage-mode: " . $this->api->getSimpleWarp()->getWarpManager()->getFlag(),
            "essentials-support: " . ($this->api->getSimpleWarp()->getConfig()->get("essentials-support") ? 'true' : 'false'),
            json_encode($this->api->getSimpleWarp()->getWarpManager()->getWarps(), JSON_PRETTY_PRINT)
        ]);
    }


    function __toString(){
        return $this->generate();
    }

    /**
     * @return SimpleWarpAPI
     */
    public function getApi(): SimpleWarpAPI{
        return $this->api;
    }
}
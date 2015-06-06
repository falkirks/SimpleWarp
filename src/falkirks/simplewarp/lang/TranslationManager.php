<?php
namespace falkirks\simplewarp\lang;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\store\DataStore;
use falkirks\simplewarp\store\Reloadable;
use falkirks\simplewarp\store\Saveable;

/**
 * This class is currently not implemented in any of SimpleWarp's utilities
 * Class TranslationManager
 * @package falkirks\simplewarp\lang
 */
class TranslationManager {
    /** @var  SimpleWarpAPI */
    private $api;
    /** @var  DataStore */
    private $store;

    public function __construct(SimpleWarpAPI $api, DataStore $store){
        $this->api = $api;
        $this->store = $store;

    }
    public function get($name){
        return $this->store[$name];
    }
    protected function registerDefaults(){

    }
    protected function registerDefault($name, $text){
        if(!isset($this->store[$name])){
            $this->store[$name] = $text;
        }
    }
    public function reload(){
        if($this->store instanceof Reloadable){
            $this->store->reload();
        }
    }
    protected function save(){
        if($this->store instanceof Saveable){
            $this->store->save();
        }
    }

}
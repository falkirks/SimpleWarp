<?php
namespace falkirks\simplewarp\store;


use pocketmine\utils\Config;

class YAMLStore extends StandardStore implements Saveable, Reloadable{
    /** @var Config  */
    private $config;
    public function __construct(Config $config){
        $this->config = $config;
    }

    public function add($name, $warp){
        $past = $this->config->get($name, null);
        $this->config->set($name, $warp);
        $this->config->save();
        return $past;
    }
    public function get($name){
        return $this->config->get($name, null);
    }

    public function remove($name){
        $past = $this->config->get($name, null);
        $this->config->remove($name);
        $this->config->save();
        return $past;
    }

    public function clear(){
        $this->config->setAll([]);
        $this->config->save();
    }

    public function reload(){
        $this->config->reload();
    }
    /**
     * Returns something which can be used to iterate
     * over the store.
     * @return mixed
     */
    public function getIterator(){
       return $this->config->getAll();
    }

    public function save(){
        $this->config->save();
    }

}
<?php

namespace falkirks\simplewarp\store;


abstract class StandardStore implements DataStore{
    public function addAll($warps){
        foreach($warps as $name => $warp){
            $this->add($name, $warp);
        }
    }
    public function removeAll($warps){
        foreach($warps as $warp){
            $this->remove($warp);
        }
    }
    public function exists($name): bool{
        return $this->get($name) !== null;
    }

}
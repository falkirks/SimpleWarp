<?php
namespace falkirks\simplewarp;


use falkirks\simplewarp\api\SimpleWarpAPI;
use falkirks\simplewarp\store\DataStore;
use falkirks\simplewarp\store\Reloadable;
use falkirks\simplewarp\store\Saveable;
use falkirks\simplewarp\utils\WeakPosition;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\network\protocol\DataPacket;
use pocketmine\utils\TextFormat;
use Traversable;

class WarpManager implements \ArrayAccess, \IteratorAggregate{
    const MEMORY_TILL_CLOSE = 0;
    const FLUSH_ON_CHANGE = 1;
    /**
     * This option is pretty scary :(
     */
    const NO_MEMORY_STORE = 2;

    /** @var SimpleWarpAPI  */
    private $api;
    /** @var DataStore  */
    private $store;

    /** @var  Warp[] */
    private $warps;

    private $flag;

    public function __construct(SimpleWarpAPI $api, DataStore $store, $flag = WarpManager::MEMORY_TILL_CLOSE){
        $this->api = $api;
        $this->store = $store;
        $this->flag = $flag;
        $this->warps = [];
        if($this->flag < 2){
            $this->warps = $this->loadWarps();
        }
    }
    protected function reloadStore(){
        if($this->flag >= 2 && $this->store instanceof Reloadable){
            $this->store->reload();
        }
    }
    protected function saveStore($force = false){
        if(($this->flag > 0 || $force) && $this->store instanceof Saveable){
            $this->store->save();
        }
    }
    protected function loadWarps(){
        $out = [];
        foreach($this->store->getIterator() as $name => $data){
            $out[$name] = $this->warpFromData($name, $data);
        }
        return $out;
    }
    /**
     * WARNING
     * This function is for internal use only.
     */
    public function saveAll(){
        if($this->flag === 0){
            $this->store->clear();
            foreach($this->warps as $warp){
                $this->store->add($warp->getName(), $this->warpToData($warp));
            }
            $this->saveStore(true);
        }
    }
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset){
        $this->reloadStore();
        if(isset($this->warps[$offset]) || ($this->flag >= 2 && $this->store->exists($offset))){
            return true;
        }
        return false;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset){
        if($this->flag >= 2){
            $this->reloadStore();
            return $this->warpFromData($offset, $this->store->get($offset));
        }
        return isset($this->warps[$offset]) ? $this->warps[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value){
        if($value instanceof Warp && $value->getName() === $offset) {
            if($this->flag < 2) {
                $this->warps[$offset] = $value;
            }

            if ($this->flag >= 1) {
                $this->store->add($offset, $this->warpToData($value));
                $this->saveStore();
            }
        }
        else{
            //TODO report failure
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset){
        if($this->flag < 2){
            unset($this->warps[$offset]);
        }

        if($this->flag >= 1){
            $this->store->remove($offset);
            $this->saveStore();
        }
    }

    /**
     * This method requires the key of the warp in order
     * to construct a warp object
     * @param $name
     * @param array $array
     * @return Warp
     * @throws \Exception
     */
    protected function warpFromData($name, array $array){
        if(isset($array["level"]) && isset($array["x"]) && isset($array["y"]) && isset($array["z"]) && isset($array["public"])){ // This is an internal warp
            return new Warp($name, new Destination(new WeakPosition($array["x"], $array["y"], $array["z"], $array["level"])), $array["public"]);
        }
        elseif(isset($array["address"]) && isset($array["port"]) && isset($array["public"])) {
            return new Warp($name, new Destination($array["address"], $array["port"]), $array["public"]);
        }

        $this->api->getSimpleWarp()->getLogger()->critical("A warp with the name " . TextFormat::AQUA . $name . TextFormat::RESET . " is incomplete. It will be removed automatically when your server stops.");
        return null;
    }

    /**
     * In order to pass data to a DataStore
     * a key is needed. Typically one should
     * use $warp->getName()
     * @param Warp $warp
     * @return array
     */
    protected function warpToData(Warp $warp){
        if($warp->getDestination()->isInternal()) {
            //TODO implement yaw and pitch
            $pos = $warp->getDestination()->getPosition();
            return [
                "x" => $pos->getX(),
                "y" => $pos->getY(),
                "z" => $pos->getZ(),
                "level" => ($pos instanceof WeakPosition ? $pos->getLevelName() : $pos->getLevel()->getName()),
                "public" => $warp->isPublic()
            ];
        }
        return [
            "address" => $warp->getDestination()->getAddress(),
            "port" => $warp->getDestination()->getPort(),
            "public" => $warp->isPublic()
        ];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator(){
        if($this->flag >= 2){
            return $this->loadWarps();
        }
        return $this->warps;
    }

}
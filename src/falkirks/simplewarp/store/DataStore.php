<?php
namespace falkirks\simplewarp\store;

/**
 * This interface deals with the storage of arbitrary warp
 * data in a key-value store.
 *
 * Interface WarpStore
 * @package falkirks\simplewarp\store
 */
interface DataStore {
    public function addAll($warps);
    public function removeAll($warps);
    public function exists($name);

    /**
     * This method takes a $name string and a $warp array and
     * returns the previous value that occupied $name or null.
     * @param $name
     * @param $warp
     * @return mixed
     */
    public function add($name, $warp);

    /**
     * @param $name
     * @return mixed
     */
    public function get($name);
    public function remove($name);
    public function clear();

    /**
     * Returns something which can be used to iterate
     * over the store.
     * @return mixed
     */
    public function getIterator();
}
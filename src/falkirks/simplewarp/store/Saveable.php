<?php
/**
 * Created by PhpStorm.
 * User: noahheyl
 * Date: 2015-06-04
 * Time: 8:28 PM
 */

namespace falkirks\simplewarp\store;


interface Saveable extends DataStore{
    public function save();
}
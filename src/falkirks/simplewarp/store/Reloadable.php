<?php
namespace falkirks\simplewarp\store;


interface Reloadable extends DataStore{
    public function reload();
}
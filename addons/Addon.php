<?php
namespace Sbnc\Addons;

abstract class Addon {

    protected $master;

    public function __construct(&$master) {
        $this->master = $master;
        $this->init();
    }

    public function before() {

    }

    public function after() {

    }

}

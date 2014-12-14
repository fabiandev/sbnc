<?php
namespace Sbnc\Modules;

abstract class Module {

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
<?php
namespace sbnc\modules;

abstract class Module {

    protected $master;
    protected $enabled = false;

    public function is_enabled() {
        return $this->enabled;
    }

    public function is_disabled() {
        return !$this->is_enabled();
    }

    public function __construct(&$master) {
        $this->master = $master;
        $this->init();
    }

    public function before() {

    }

    public function after() {

    }

}
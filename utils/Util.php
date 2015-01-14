<?php
namespace sbnc\utils;

abstract class Util
{

    protected $enabled = false;

    public function is_enabled()
    {
        return $this->enabled;
    }

    public function is_disabled()
    {
        return !$this->is_enabled();
    }

    public function __construct()
    {
        $this->init();
    }

    public function before()
    {

    }

    public function after()
    {

    }

}

<?php
namespace sbnc\utils;

abstract class Util
{

    protected $enabled = false;

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function isDisabled()
    {
        return !$this->isEnabled();
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

<?php
namespace sbnc\modules;

abstract class Module
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
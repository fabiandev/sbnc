<?php
namespace sbnc\core;

abstract class Component implements ComponentInterface
{

    protected $enabled = false;

    public function __construct()
    {

    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    public function init()
    {

    }

    public function before()
    {

    }

    public function after()
    {

    }

}
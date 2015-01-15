<?php
namespace sbnc\modules;

Interface ModuleInterface
{

    public function __construct();

    public function isEnabled();

    public function isDisabled();

    public function before();

    public function after();

}

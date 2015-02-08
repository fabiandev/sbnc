<?php
namespace sbnc\core;

Interface ComponentInterface
{

    public function __construct();

    public function enable();

    public function disable();

    public function isEnabled();

    public function isDisabled();

    public function init();

    public function before();

    public function after();

}

<?php
namespace sbnc\utils;

Interface UtilInterface
{

    public function __construct();

    public function isEnabled();

    public function isDisabled();

    public function before();

    public function after();

}

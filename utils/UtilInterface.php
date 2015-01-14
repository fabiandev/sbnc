<?php
namespace sbnc\utils;

Interface UtilInterface
{

    public function __construct();

    public function is_enabled();

    public function is_disabled();

    public function before();

    public function after();

}

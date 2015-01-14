<?php
namespace sbnc\modules;

Interface ModuleInterface {

    public function __construct();
    public function is_enabled();
    public function is_disabled();
    public function before();
    public function after();

}

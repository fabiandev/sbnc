<?php
namespace sbnc\addons;

Interface AddonInterface {

    public function __construct();
    public function is_enabled();
    public function is_disabled();
    public function before();
    public function after();

}

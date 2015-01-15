<?php
namespace sbnc\addons;

Interface AddonInterface
{

    public function __construct();

    public function isEnabled();

    public function isDisabled();

    public function before();

    public function after();

}

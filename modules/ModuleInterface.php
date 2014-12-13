<?php
namespace Sbnc;

Interface ModuleInterface {

    public function __construct(&$master);
    public function check($master);

}

<?php
namespace Fw\Sbnc\Modules;

class hidden {

    public function __construct(&$master) {
        $master['fields']['check'] = false;
    }

    public function check($master) {

    }

}
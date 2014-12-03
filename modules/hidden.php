<?php
namespace Fw\Sbnc\Modules;

class hidden implements module {

    public function __construct(&$master) {
        $master['fields']['check'] = false;
    }

    public function check($master) {

    }

}
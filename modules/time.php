<?php
namespace Fw\Sbnc\Modules;

class time implements module {

    private $options = [
        'min' => 1,
        'max' => 3600
    ];

    public function __construct(&$master) {
        $master['fields']['time'] = time();
    }

    public function check($master) {

    }

}
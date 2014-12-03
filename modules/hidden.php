<?php
namespace Sbnc\Modules;

class hidden {

    /**
     * Adds a field to sbnc
     *
     * @param $master
     */
    public function __construct(&$master) {
        $master['fields']['check'] = false;
    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check($master) {

    }

}
<?php
namespace Fw\Sbnc\Modules;

class gestures {

    /*
     * Options for checking keyboard and mouse usage.
     *
     * auto:     checks mouse and keyboard on first request
     *           and only mouse when errors occured
     *
     * mouse:    checks mouse usage only
     *
     * keyboard: checks keyboard usage only
     *
     * @var array
     */
    private $options = [
        'mode' => 'auto'
    ];

    public function __construct(&$master) {
        $master['fields']['mouse']    = false;
        $master['fields']['keyboard'] = false;
    }

    public function check($master) {

    }

}
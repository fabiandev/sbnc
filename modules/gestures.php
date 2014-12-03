<?php
namespace Fw\Sbnc\Modules;

class gestures {

    /*
     * Options for checking keyboard and mouse usage.
     *
     * auto:     checks mouse and keyboard on first request
     *           and only mouse when errors occurred
     * mouse:    checks mouse usage only
     * keyboard: checks keyboard usage only
     *
     * @var array
     */
    private $options = [
        'mode' => 'auto'
    ];

    /**
     * Adds two fields to sbnc
     *
     * @param $master
     */
    public function __construct(&$master) {
        $master['fields']['mouse']    = false;
        $master['fields']['keyboard'] = false;
    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check($master) {

    }

}
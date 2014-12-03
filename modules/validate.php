<?php
namespace Fw\Sbnc\Modules;

class validate {

    /**
     * Module options
     *
     * The array-value holds the form-field(s) name(s) for the
     * checks to be applied on. The checks are defined in the
     * key:
     *
     * email: check for a valid email
     * url:   check for a valid url
     *
     * @var array
     */
    private $options = [
        'email' => ['email', 'mail'],
        'url'   => ['url', 'link', 'web']
    ];

    /**
     * @param $master
     */
    public function __construct(&$master) {

    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check($master) {

    }

}
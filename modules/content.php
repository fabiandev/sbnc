<?php
namespace Sbnc\Modules;

class content {

    /**
     * Module options
     *
     * Defines which field names should be checked for
     * spam contents.
     *
     * @var array
     */
    private $options = [
        'comment', 'message', 'msg', 'post'
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
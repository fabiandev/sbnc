<?php
namespace Sbnc\Modules;

class Content extends Module implements ModuleInterface {

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


    protected function init() {

    }


    /**
     * Starts module check
     *
     * @param $master
     */
    public function check() {

    }

}
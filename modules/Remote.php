<?php
namespace Sbnc\Modules;

class Remote extends Module implements ModuleInterface {

    /**
     * Module options
     *
     * Set email field names to use for email check or false.
     * Set other checks to on (true) or off (false)
     *
     * @var array
     */
    private $options = [
        'email'  => ['email', 'mail'],
        'ip'     => true,
        'header' => true
    ];

    protected function init() {

    }

    public function check() {

    }

}
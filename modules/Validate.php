<?php
namespace Sbnc\Modules;

class Validate extends Module implements ModuleInterface {

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

    protected function init() {

    }

    public function check() {

    }

}
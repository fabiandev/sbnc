<?php
namespace Sbnc\Modules;

class Gestures extends Module implements ModuleInterface {

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

    protected function init() {
        $this->master['fields']['mouse']    = null;
        $this->master['fields']['keyboard'] = null;
    }

    public function check() {

    }

}
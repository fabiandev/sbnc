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
    private $errors = [
        'mouse'     => 'Spam! Mouse not used',
        'keyboard'  => 'Spam! Keyboard not used',
        'js'        => 'JavaScript must be activated'
    ];

    private $options = [
        'mode' => ['mouse', 'js']
    ];

    protected function init() {
        $this->master['fields']['mouse']    = null;
        $this->master['fields']['keyboard'] = null;
    }

    public function check() {
        if (in_array('js', $this->options['mode'])) {
            if (!isset($this->master['request']['js']) || strcmp($this->master['request']['js'], 'true') !== 0) {
                array_push($this->master['errors'], $this->errors['js']);
                return;
            }
        }

        if (in_array('keyboard', $this->options['mode'])) {
            if (!isset($this->master['request']['keyboard']) || strcmp($this->master['request']['keyboard'], 'true') !== 0) {
                array_push($this->master['errors'], $this->errors['keyboard']);
            }
        }

        if (in_array('mouse', $this->options['mode'])) {
            if (!isset($this->master['request']['mouse']) || strcmp($this->master['request']['mouse'], 'true') !== 0) {
                array_push($this->master['errors'], $this->errors['mouse']);
            }
        }
    }

}
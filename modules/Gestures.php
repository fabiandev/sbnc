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
        'mouse'     => 'Mouse not used',
        'keyboard'  => 'Keyboard not used',
        'js'        => 'JavaScript must be activated'
    ];

    private $options = [
        'mode' => ['mouse', 'js']
    ];

    protected function init() {
        $this->enabled = true;
        $this->master['fields']['mouse']    = null;
        $this->master['fields']['keyboard'] = null;
    }

    public function check() {
        if (in_array('js', $this->options['mode'])) {
            if (!isset($this->master['request']['js']) || strcmp($this->master['request']['js'], 'true') !== 0) {
                array_push($this->master['errors'], $this->errors['js']);
                $this->master['utils']['LogMessages']->log('spam-gestures', 'JavaScript not enabled');
                return;
            }
        }

        if (in_array('keyboard', $this->options['mode'])) {
            if (!isset($this->master['request']['keyboard']) || strcmp($this->master['request']['keyboard'], 'true') !== 0) {
                array_push($this->master['errors'], $this->errors['keyboard']);
                $this->master['utils']['LogMessages']->log('spam-gestures', 'Keyboard not used');
            }
        }

        if (in_array('mouse', $this->options['mode'])) {
            if (!isset($this->master['request']['mouse']) || strcmp($this->master['request']['mouse'], 'true') !== 0) {
                array_push($this->master['errors'], $this->errors['mouse']);
                $this->master['utils']['LogMessages']->log('spam-gestures', 'Mouse not used');
            }
        }
    }

}
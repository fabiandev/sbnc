<?php
namespace sbnc\modules;
use sbnc\Sbnc;

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
        Sbnc::add_field('mouse', null);
        Sbnc::add_field('keyboard', null);
        Sbnc::add_field('js', null);
        Sbnc::add_javascript($this->get_js());
    }

    public function check() {
        if (in_array('js', $this->options['mode'])) {
            $js_value = Sbnc::request('js');
            if (empty($js_value) || strcmp($js_value, 'true') !== 0) {
                Sbnc::add_error($this->errors['js']);
                Sbnc::util('LogMessages')->log('spam-gestures', 'JavaScript not enabled');
                return;
            }
        }

        if (in_array('keyboard', $this->options['mode'])) {
            $key_value = Sbnc::request('keyboard');
            if (empty($key_value) || strcmp($key_value, 'true') !== 0) {
                Sbnc::add_error($this->errors['keyboard']);
                Sbnc::util('LogMessages')->log('spam-gestures', 'Keyboard not used');
            }
        }

        if (in_array('mouse', $this->options['mode'])) {
            $mouse_value = Sbnc::request('mouse');
            if (empty($mouse_value) || strcmp($mouse_value, 'true') !== 0) {
                Sbnc::add_error($this->errors['mouse']);
                Sbnc::util('LogMessages')->log('spam-gestures', 'Mouse not used');
            }
        }
    }

    protected function get_js()
    {
        $prefix = Sbnc::options('prefix')[0];
        $keyboard_field = $prefix . 'keyboard';
        $mouse_field = $prefix . 'mouse';
        $js_field = $prefix . 'js';

        $js  = 'sbnc.gestures = (function() { var init, usedKeyboard, usedMouse;';
        $js .= 'var keyboardField, mouseField, jsField;';
        $js .= 'init = function() {';
        $js .= 'keyboardField = document.getElementById("'.$keyboard_field.'");';
        $js .= 'mouseField = document.getElementById("'.$mouse_field.'");';
        $js .= 'jsField = document.getElementById("'.$js_field.'");';
        $js .= 'jsField.value = "true";';
        $js .= 'window.onkeyup = usedKeyboard;';
        $js .= 'window.onmousemove = usedMouse;';
        $js .= '};';
        $js .= 'usedKeyboard = function() {  keyboardField.value = "true"; };';
        $js .= 'usedMouse = function() { mouseField.value = "true"; };';
        $js .= 'return { init: init };';
        $js .= '}());';
        $js .= 'sbnc.gestures.init();';

        return $js;
    }

}
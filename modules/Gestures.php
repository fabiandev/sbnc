<?php
namespace sbnc\modules;

use sbnc\Sbnc;
use sbnc\core\Module;

/**
 * Class Gestures
 *
 * checks for client gestures like if a mouse or keyboard was used.
 * also checks if javascript is enabled
 *
 * @package sbnc\modules
 */
class Gestures extends Module
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * May be disabled if any inconsistencies occur
     *
     * @var bool Enable or disable module
     */
    protected $enabled = true;

    /*
     * Options for checking keyboard and mouse usage.
     *
     * mouse:    checks mouse usage only
     * keyboard: checks keyboard usage only
     * js:       if defined, javascript must be enabled
     *
     * @var array Options
     */
    private $use = ['mouse', 'js'];

    /**
     * Define your custom error messages
     *
     * @var array Error messages
     */
    private $errors = [
        'mouse' => 'Mouse not used',
        'keyboard' => 'Keyboard not used',
        'js' => 'JavaScript must be activated'
    ];

    ######################################################################################
    ######################################################################################


    public function init()
    {
        Sbnc::addField('mouse', null);
        Sbnc::addField('keyboard', null);
        Sbnc::addField('js', null);
        Sbnc::addJavascript($this->getJs());
    }

    public function check()
    {
        if (in_array('js', $this->use)) {
            $js_value = Sbnc::request('js');
            if (empty($js_value) || strcmp($js_value, 'true') !== 0) {
                Sbnc::addError($this->errors['js']);
                Sbnc::log('spam-gestures', 'JavaScript not enabled');
                return;
            }
        }

        if (in_array('keyboard', $this->use)) {
            $key_value = Sbnc::request('keyboard');
            if (empty($key_value) || strcmp($key_value, 'true') !== 0) {
                Sbnc::addError($this->errors['keyboard']);
                Sbnc::log('spam-gestures', 'Keyboard not used');
            }
        }

        if (in_array('mouse', $this->use)) {
            $mouse_value = Sbnc::request('mouse');
            if (empty($mouse_value) || strcmp($mouse_value, 'true') !== 0) {
                Sbnc::addError($this->errors['mouse']);
                Sbnc::log('spam-gestures', 'Mouse not used');
            }
        }
    }

    protected function getJs()
    {
        $keyboard_field = Sbnc::getFieldName('keyboard');
        $mouse_field = Sbnc::getFieldName('mouse');
        $js_field = Sbnc::getFieldName('js');

        $js = 'sbnc.gestures = (function() { var init, usedKeyboard, usedMouse;';
        $js .= 'var keyboardField, mouseField, jsField;';
        $js .= 'init = function() {';
        $js .= 'keyboardField = document.getElementById("' . $keyboard_field . '");';
        $js .= 'mouseField = document.getElementById("' . $mouse_field . '");';
        $js .= 'jsField = document.getElementById("' . $js_field . '");';
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
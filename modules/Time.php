<?php
namespace sbnc\modules;

use sbnc\Sbnc;
use sbnc\core\Module;

/**
 * Class Time
 *
 * Checks for too fast or too late form submits
 *
 * @package sbnc\modules
 */
class Time extends Module
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

    /**
     * Define which checks to use
     *
     * @var array
     */
    private $use = ['min', 'max'];

    /**
     * Set the minimum and maximum time a submit has to take.
     * Time in seconds.
     *
     * @var array Options
     */
    private $options = [
        'min' => 1,
        'max' => 600
    ];

    /**
     * Set your custom error messages
     *
     * @var array Error messages
     */
    private $errors = [
        'min' => 'Sorry, this was too fast.',
        'max' => 'Sorry, this took too long. Try again!'
    ];

    ######################################################################################
    ######################################################################################


    public function init()
    {
        Sbnc::addField('time', base64_encode(time()));
    }

    public function check()
    {
        $now = time();
        $time = base64_decode(Sbnc::request('time'));
        $diff = $now - $time;

        if (in_array('min', $this->use) && $diff < $this->options['min']) {
            Sbnc::addError($this->errors['min']);
            Sbnc::log('spam-fast-submit', 'Submit too fast: < ' . $this->options['min']);
        } elseif (in_array('max', $this->use) && $diff > $this->options['max']) {
            Sbnc::addError($this->errors['max']);
            Sbnc::log('spam-slow-submit', 'Submit too slow: > ' . $this->options['max']);
        }
    }

}
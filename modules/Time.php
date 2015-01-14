<?php
namespace sbnc\modules;

use sbnc\Sbnc;

/**
 * Class Time
 *
 * Checks for too fast or too late form submits
 *
 * @package sbnc\modules
 */
class Time extends Module implements ModuleInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Set the minimum and maximum time a submit has to take.
     * Time in seconds.
     *
     * @var array
     */
    private $options = [
        'min' => 1,
        'max' => 600
    ];

    /**
     * Set your custom error messages
     *
     * @var array
     */
    private $errors = [
        'min' => 'Sorry, this was too fast.',
        'max' => 'Sorry, this took too long. Try again!'
    ];

    ######################################################################################
    ######################################################################################

    protected function init()
    {
        $this->enabled = true;
        Sbnc::add_field('time', time());
    }

    public function check()
    {
        $now = time();
        $time = Sbnc::request('time');
        $diff = $now - $time;

        if ($diff < $this->options['min']) {
            Sbnc::add_error($this->errors['min']);
            Sbnc::util('LogMessages')->log('spam-fast-response', 'Submit too fast: < ' . $this->options['min']);
        } elseif ($diff > $this->options['max']) {
            Sbnc::add_error($this->errors['max']);
            Sbnc::util('LogMessages')->log('spam-timeout', 'Submit too slow: > ' . $this->options['max']);
        }
    }

}
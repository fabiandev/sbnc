<?php
namespace sbnc\modules;

use sbnc\Sbnc;
use sbnc\core\Module;

/**
 * Class Referrer
 *
 * Form processing must be the same file and host as the form field.
 * Only checked if "HTTP_REFERER" is set.
 *
 * @package sbnc\modules
 */
class Referrer extends Module
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
     * Set your custom error message
     *
     * @var array Error messages
     */
    private $errors = [
        'error' => 'Form must be processed on same page and host as the request came from!'
    ];

    ######################################################################################
    ######################################################################################


    public function check()
    {
        if ((isset($_SERVER['HTTP_REFERER']) && !stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))) {
            Sbnc::addError($this->errors['error']);
            Sbnc::log('spam-referrer', 'HTTP Referrer was different from Host: ' . $_SERVER['HTTP_REFERER']);
        }
    }

}
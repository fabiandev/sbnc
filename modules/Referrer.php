<?php
namespace sbnc\modules;

use sbnc\Sbnc;

/**
 * Class Referrer
 *
 * Form processing must be the same file and host as the form field.
 * Only checked if "HTTP_REFERER" is set.
 *
 * @package sbnc\modules
 */
class Referrer extends Module implements ModuleInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

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


    protected function init()
    {
        $this->enabled = true;
    }


    public function check()
    {
        if ((isset($_SERVER['HTTP_REFERER']) && !stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))) {
            Sbnc::addError($this->errors['error']);
            Sbnc::log('spam-referrer', 'HTTP Referrer was different from Host: ' . $_SERVER['HTTP_REFERER']);
        }
    }

}
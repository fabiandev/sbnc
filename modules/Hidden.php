<?php
namespace sbnc\modules;

use sbnc\Sbnc;
use sbnc\core\Module;

/**
 * Class Hidden
 *
 * A hidden field must be present and empty
 *
 * @package sbnc\modules
 */
class Hidden extends Module
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
     * Define your custom error message.
     * %field% will be replaced by the field name
     *
     * @var array Error messages
     */
    private $errors = [
        'error' => '%field% is not empty or has been modified'
    ];

    ######################################################################################
    ######################################################################################


    public function init()
    {
        Sbnc::addField('check', null);
    }

    public function check()
    {
        $hidden_value = Sbnc::request('check');
        if (Helpers::isEmpty($hidden_value)) {
            $err = str_replace('%field%', 'check', $this->errors['error']);
            Sbnc::addError($err);
            Sbnc::log('spam-hidden', $err);
        }
    }

}
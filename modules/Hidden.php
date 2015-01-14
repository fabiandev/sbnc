<?php
namespace sbnc\modules;
use sbnc\Sbnc;

/**
 * Class Hidden
 *
 * A hidden field must be present and empty
 *
 * @package sbnc\modules
 */
class Hidden extends Module implements ModuleInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Define your custom error message.
     * %field% will be replaced by the field name
     *
     * @var array
     */
    private $errors = [
        'error' => '%field% is not empty or has been modified'
    ];

    ######################################################################################
    ######################################################################################


    protected function init()
    {
        $this->enabled = true;
        Sbnc::add_field('check', null);
    }

    public function check()
    {
        $hidden_value = Sbnc::request('check');
        if ($hidden_value === null || strlen(trim($hidden_value)) != 0) {
            $err = str_replace('%field%', 'check', $this->errors['error']);
            Sbnc::add_error($err);
            Sbnc::util('LogMessages')->log('spam-hidden', 'Hidden field was not empty or has been modified/removed: ' . $hidden_value);
        }
    }

}
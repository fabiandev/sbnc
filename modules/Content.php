<?php
namespace sbnc\modules;

/**
 * Class Content
 * @package sbnc\modules
 */
class Content extends Module implements ModuleInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Module options
     *
     * Defines which field names should be checked for
     * spam contents.
     *
     * @var array
     */
    private $options = [
        'comment', 'message', 'msg', 'post'
    ];

    ######################################################################################
    ######################################################################################


    protected function init()
    {
        $this->enabled = true;
    }


    public function check()
    {

    }

}
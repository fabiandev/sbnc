<?php
namespace Sbnc\Modules;

/**
 * sbnc content module
 *
 * Checks for spam content.
 *
 * @package    Sbnc
 * @subpackage Modules
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.1
 * @link       https://github.com/fabianweb/sbnc/modules/
 */
class Content extends Module implements ModuleInterface {

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


    protected function init() {

    }


    /**
     * Starts module check
     *
     * @param $master
     */
    public function check() {

    }

}
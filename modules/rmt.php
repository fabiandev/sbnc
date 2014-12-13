<?php
namespace Sbnc\Modules;

/**
 * sbnc remote module
 *
 * Checks against remote spam lists
 *
 * @package    Sbnc
 * @subpackage Modules
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.1
 * @link       https://github.com/fabianweb/sbnc/modules/
 */
class Remote implements Module {

    /**
     * Module options
     *
     * Set email field names to use for email check or false.
     * Set other checks to on (true) or off (false)
     *
     * @var array
     */
    private $options = [
        'email'  => ['email', 'mail'],
        'ip'     => true,
        'header' => true
    ];

    /**
     * @param $master
     */
    public function __construct(&$master) {

    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check($master) {

    }

}
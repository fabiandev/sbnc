<?php
namespace Sbnc\Modules;

/**
 * sbnc time module
 *
 * Checks how long it took, until the form was submitted.
 *
 * @package    Sbnc
 * @subpackage Modules
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.1
 * @link       https://github.com/fabianweb/sbnc/modules/
 */
class Time implements ModuleInterface {

    /**
     * Module options
     *
     * min: minimum time in seconds
     * max: maximum time in seconds
     *
     * @var array
     */
    private $options = [
        'min' => 1,
        'max' => 3600
    ];

    /**
     * Adds a field to sbnc
     *
     * @param $master
     */
    public function __construct(&$master) {
        $master['fields']['time'] = time();
    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check($master) {

    }

}
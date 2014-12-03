<?php
namespace Sbnc\Modules;

/**
 * sbnc hidden module
 *
 * Simply looks for hidden field that needs to be empty.
 *
 * @package    Sbnc
 * @subpackage Modules
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.1
 * @link       https://github.com/fabianweb/sbnc/modules/
 */
class hidden {

    /**
     * Adds a field to sbnc
     *
     * @param $master
     */
    public function __construct(&$master) {
        $master['fields']['check'] = false;
    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check($master) {

    }

}
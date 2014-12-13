<?php
namespace Sbnc\Modules;

/**
 * sbnc validate module
 *
 * Some validation rules that can be applied on custom fields
 *
 * @package    Sbnc
 * @subpackage Modules
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.1
 * @link       https://github.com/fabianweb/sbnc/modules/
 */
class Validate implements ModuleInterface {

    /**
     * Module options
     *
     * The array-value holds the form-field(s) name(s) for the
     * checks to be applied on. The checks are defined in the
     * key:
     *
     * email: check for a valid email
     * url:   check for a valid url
     *
     * @var array
     */
    private $options = [
        'email' => ['email', 'mail'],
        'url'   => ['url', 'link', 'web']
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
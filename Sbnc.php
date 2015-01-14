<?php
namespace sbnc;
//require 'loader.php';

/**
 * sbnc
 *
 * Blocks Spam without any human interaction.
 *
 * @package    Sbnc
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.2
 * @link       https://github.com/fabianweb/sbnc
 */

class Sbnc
{

    protected static $core;

    protected static $modules = [
        'Time',
        'Hidden',
        'Gestures',
        'Content',
        'Validate',
        'RemoteHttpBlacklist'
    ];

    protected static $addons = [
        'Flasher'
    ];


    protected static $utils = [
        'FlashMessages', // required
        'LogMessages' // required
    ];

    /**
     * Options for sbnc.
     * The second entry for the prefix option should be changed. It
     * is the name for the prefix field, that holds the random prefix
     * for other fields (begin it with a letter!).
     *
     * @var array
     */
    protected static $options = [
        'prefix' => ['random', 'a86jg5'],
        'javascript' => true,
        'html5' => true
    ];

    public static function __callStatic($name, $params) {
        self::init();
        return Core::call($name, $params);
    }

    public static function core() {
        return self::$core;
    }

    public static function start() {
        self::init();
        self::$core->start();
    }

    public static function init() {
        static $initialized = false;
        if (!$initialized) {
            require_once __DIR__.'/loader.php';
            self::$core = new Core([
                'modules' => self::$modules,
                'addons'  => self::$addons,
                'utils'   => self::$utils,
                'options' => self::$options
            ]);
            $initialized = true;
        }
    }

}

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
    private static $modules = [
        'Time',
        'Hidden',
        'Gestures',
        'Content',
        'Validate',
        'RemoteHttpBlacklist'
    ];

    private static $addons = [
        'Flasher'
    ];


    private static $utils = [
        'FlashMessages', // required
        'LogMessages' // required
    ];

    private static $options = [
        'prefix' => ['random', 'a86jg5'],
        'javascript' => true,
        'html5' => true
    ];





    //##################################################################################################\\

    private static $core;
    private static $initialized = false;

    private function __construct() {}
    private function __destruct() {}
    private function __clone() {}

    public static function __callStatic($name, $params) {
        self::init();
        try {
            return self::$core->call($name, $params);
        } catch(\Exception $e) {
            self::print_exception($e);
        }
    }

    public static function core() {
        if (!is_object(self::$core)) {
            self::throw_exception('Core is not initialized');
        }
        return self::$core;
    }

    public static function start() {
        ob_start();
        self::init();
        self::$core->start();
    }

    private static function init() {
        if (!self::$initialized) {
            self::$initialized = true;
            require_once __DIR__.'/loader.php';
            self::$core = new Core([
                'modules' => self::$modules,
                'addons'  => self::$addons,
                'utils'   => self::$utils,
                'options' => self::$options
            ]);
            self::$core->init();
        }
    }

    public static function throw_exception($message) {
        self::print_exception(new \Exception($message));
    }

    public static function print_exception(\Exception $e) {
        ob_clean();
        $err  = '<h3>Sorry, there was an error!</h3>';
        $err .= '<pre>';
        $err .= '<span style="font-weight:600">' . $e->getMessage() . '</span>';
        $err .= ' in ' . $e->getFile() . ' on line ' . $e->getLine() . ':';
        $err .= '<br><br>';
        $err .= $e->getTraceAsString();
        $err .= '</pre>';
        echo $err;
        ob_end_flush();
        exit;
    }

}

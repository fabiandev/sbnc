<?php
namespace sbnc;

/**
 * Class Sbnc
 *
 * Blocks Spam without any human interaction.
 *
 * @package    sbnc
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.2
 * @link       https://github.com/fabianweb/sbnc
 */

class Sbnc
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Defined modules will be loaded
     *
     * @var array
     */
    private static $modules = [
        'Time',
        'Hidden',
        'Gestures',
        'Content',
        'Validate',
        'RemoteHttpBlacklist',
        'Referrer'
    ];

    /**
     * Defined addons will be loaded
     *
     * @var array
     */
    private static $addons = [
        'Flasher'
    ];

    /**
     * Defined utils will be loaded
     *
     * @var array
     */
    private static $utils = [
        'FlashMessages', // required
        'LogMessages' // required
    ];

    /**
     * Set some options here:
     *
     * - prefix
     *      set to random to generate a form field prefix on every request.
     *      second parameter is the field name, that holds the generated random value
     *      change this value and begin it with a letter!
     *
     * - javascript
     *      set to false if you don't want to use any javascript
     *
     * -html5
     *      set to false if you don't use the html5 doctype
     *
     * @var array
     */
    private static $options = [
        'prefix' => ['random', 'a86jg5'],
        'javascript' => true,
        'html5' => true
    ];

    ######################################################################################
    ######################### DO NOT CHANGE CODE BELOW THIS LINE #########################
    #########################   UNLESS YOU KNOW WHAT YOU DO :)   #########################
    ######################################################################################

    /**
     * Instance of the sbnc core
     *
     * @var
     */
    private static $core;

    /**
     * Set to true if sbnc has been initialized
     *
     * @var bool
     */
    private static $initialized = false;

    // do not allow to create an instance of sbnc
    private function __construct() {}
    private function __destruct() {}
    private function __clone() {}

    /*
     * Catch all static calls and let the core handle the request
     *
     * @param $name called method name
     * @param $param array of the parameters
     */
    public static function __callStatic($name, $params)
    {
        self::init();
        try {
            return self::$core->call($name, $params);
        } catch (\Exception $e) {
            self::print_exception($e);
        }
    }

    /**
     * Returns the core instance
     *
     * @return mixed
     */
    public static function core()
    {
        if (!is_object(self::$core)) {
            self::throw_exception('Core is not initialized');
        }
        return self::$core;
    }

    /**
     * Starts initialization and triggers sbnc start
     *
     * @param null $action user function to be called during checks if provided
     */
    public static function start($action = null)
    {
        self::init();
        self::$core->start($action);
    }

    /**
     * Load class autoloader, create core instance and initialize core
     */
    private static function init()
    {
        if (!self::$initialized) {
            self::$initialized = true;
            require_once __DIR__ . '/loader.php';
            self::$core = new Core([
                'modules' => self::$modules,
                'addons' => self::$addons,
                'utils' => self::$utils,
                'options' => self::$options
            ]);
            self::$core->init();
        }
    }

    /**
     * Throws and prints an exception
     *
     * @param $message
     */
    public static function throw_exception($message)
    {
        self::print_exception(new \Exception($message));
    }

    /**
     * Prints an exception
     *
     * @param \Exception $e
     */
    public static function print_exception(\Exception $e)
    {
        $err = '<h3>Sorry, there was an error!</h3>';
        $err .= '<pre>';
        $err .= '<span style="font-weight:600">' . $e->getMessage() . '</span>';
        $err .= ' in ' . $e->getFile() . ' on line ' . $e->getLine() . ':';
        $err .= '<br><br>';
        $err .= $e->getTraceAsString();
        $err .= '</pre>';
        echo $err;
        exit;
    }

}

<?php
namespace sbnc;

// Can handle sessions if output has started before starting
// sbnc. Class must be included before headers have been sent.
//
// Simply remove the line below to disable buffering.
ob_start();

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
     * @var array Modules
     */
    private static $modules = [
        'Time',
        'Hidden',
        'Gestures',
        'Content',
        'Validate',
        'RemoteHttpBlacklist',
        'Referrer',
        'Csrf'
    ];

    /**
     * Defined addons will be loaded
     *
     * @var array Addons
     */
    private static $addons = [
        'Flasher'
    ];

    /**
     * Defined utils will be loaded
     *
     * @var array Utils
     */
    private static $utils = [
        'FlashMessages',
        'LogMessages'
    ];

    /**
     * Set some options here:
     *
     * - prefix
     *      set to random to generate a form field prefix on every request.
     *
     * - javascript
     *      set to false if you don't want to use any javascript
     *
     * -html5
     *      set to false if you don't use the html5 doctype
     *
     * @var array Options
     */
    private static $options = [
        'prefix' => 'random',
        'html5' => true
    ];

    /**
     * Set the fallback field name for the input field,
     * that holds the random value, if util FlashMessages is
     * not used. Begin it with a letter!
     *
     * @var string
     */
    private static $prefix_field_name = 'a86jg5';

    ######################################################################################
    ######################### DO NOT CHANGE CODE BELOW THIS LINE #########################
    #########################   UNLESS YOU KNOW WHAT YOU DO :)   #########################
    ######################################################################################

    /**
     * Instance of the sbnc core
     *
     * @var object Core instance
     */
    private static $core;

    /**
     * Set to true if sbnc has been initialized
     *
     * @var bool Initialization status
     */
    private static $initialized = false;

    // do not allow to create an instance of sbnc
    private function __construct()
    {
    }

    private function __destruct()
    {
    }

    private function __clone()
    {
    }

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
            self::printException($e);
        }
    }

    /**
     * Returns the core instance
     *
     * @return mixed Core instance
     */
    public static function core()
    {
        if (!is_object(self::$core)) {
            self::throwException('Core is not initialized');
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
            $prefix_type = self::$options['prefix'];
            $prefix_field = self::$prefix_field_name;

            self::$options['prefix'] = [$prefix_type, $prefix_field];

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
     * @param $message String for Exception
     */
    public static function throwException($message)
    {
        self::printException(new \Exception($message));
    }

    /**
     * Prints an exception
     *
     * @param \Exception $e
     */
    public static function printException(\Exception $e)
    {
        if (ob_get_level() > 0) ob_clean();
        $err = '<h3>Sorry, there was an error (sbnc)!</h3>';
        $err .= '<pre>';
        $err .= '<span style="font-weight:600">' . $e->getMessage() . '</span>';
        $err .= ' in ' . $e->getFile() . ' on line ' . $e->getLine() . ':';
        $err .= '<br><br>';
        $err .= $e->getTraceAsString();
        $err .= '</pre>';
        echo $err;
        if (ob_get_level() > 0) ob_end_flush();
        exit;
    }

}

<?
namespace Sbnc;

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

class sbnc {

    /**
     * Defines all modules  by it's name in the modules directory
     *
     * @var array
     */
    private $modules = [
        'time',
        'hidden',
        'gestures',
        'content',
        'validate'
    ];

    /**
     * Options for sbnc.
     * The second entry for the prefix option should be changed. It
     * is the name for the prefix field, that holds the random prefix
     * for other fields.
     *
     * @var array
     */
    private $options = [
        'prefix'      => ['random', 'a86jg5'],
        'javascript'  => true,
        'html5'       => true
    ];

    /**
     * Holds all fields that need to be included in the form.
     *
     * @var array
     */
    private $fields = [];

    /**
     * Holds the request after sending a form.
     *
     * @var array
     */
    private $request = [];

    /**
     * All errors that occur during all checks (also modules)
     *
     * @var array
     */
    private $errors = [];

    /**
     * Holds references to the request, fields, errors,
     * modules and options.
     *
     * @var array
     */
    private $master = [];


    /**
     * Changes modules array for internal use
     */
    public function __construct() {
        $this->modules = array_fill_keys($this->modules, null);
        $this->init();
    }

    /**
     * Class autoloader
     *
     * @param $class
     */
    public static function load_modules($class) {
        $split = explode('\\', $class);
        include 'modules/' . end($split) . '.php';
    }

    /**
     * List of initialization work that needs to be done
     * after the constructor in correct order.
     */
    private function init() {
        $this->init_fields();
        $this->init_master();
        $this->init_modules();
    }

    /**
     * Generates the random prefix and initializes
     * main fields.
     */
    private function init_fields() {
        if (strcmp($this->options['prefix'][0], 'random') === 0) {
            $this->options['prefix'][0] = substr(md5(microtime()),rand(0,26),4);
        }

        $this->fields = [
            'js'     => false,
            'prefix' => &$this->options['prefix'][0]
        ];
    }

    /**
     * Saves all references in $master to pass them to modules.
     */
    private function init_master() {
        $this->master = [
            'request'     => &$this->request,
            'errors'      => &$this->errors,
            'modules'     => &$this->modules,
            'options'     => &$this->options,
            'fields'      => &$this->fields,
        ];
    }

    /**
     * Loads all modules and initializes them.
     */
    private function init_modules() {
        foreach ($this->modules as $key => $value) {
            $class = __NAMESPACE__ . '\\Modules\\' . $key;
            $this->modules[$key] = new $class($this->master);
        }
    }

    /**
     * Runs all checks on the request and returns a
     * boolean, if it passed.
     *
     * @param $request
     * @return boolean
     */
    public function check($request) {
        $this->request = $request;
        foreach ($this->modules as $module) {
            $module->check($this->master);
        }

        return null;
    }

    /**
     * Returns an array, holding all errors (also from modules)
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Use this method to fill form fields with previous
     * content, if errors occurred.
     *
     * @param $name
     * @return string
     */
    public function get_value($name) {

    }

    /**
     * Generates a string, containing all fields as html code.
     *
     * @return string
     */
    public function get_fields() {
        $html = '';
        $tag_end = ($this->options['html5']) ? '' : '/';

        foreach ($this->fields as $key => $value) {
            $val = $value !== false ? $value : '';
            $id = strcmp($key, 'prefix') !== 0 ? $this->options['prefix'][0].$key : $this->options['prefix'][1];
            $html .= '<input type="text" id="'.$id.'" name="'.$id.'" value="'.$val.'" style="display:none" '.$tag_end.'>'."\n";
        }

        return $html;
    }

    /**
     * Prints what get_fields() returns.
     */
    public function print_fields() {
        echo $this->get_fields();
    }

    /**
     * Generates a string, containing all javascript code.
     *
     * @return string
     */
    public function get_js() {
        return '';
    }

    /**
     * Prints what get_js() returns.
     */
    public function print_js() {
        echo $this->get_js();
    }

}

// Required for module auto loading!
spl_autoload_register(array('Sbnc\sbnc', 'load_modules'));

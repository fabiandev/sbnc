<?php
namespace sbnc;

class Core
{
    ######################################################################################
    #########################  DO NOT CHANGE CODE IN THIS FILE   #########################
    #########################   UNLESS YOU KNOW WHAT YOU DO :)   #########################
    ######################################################################################

    /**
     * Holds instances of all modules, utils and addons
     *
     * @var array Components
     */
    protected static $components = [];

    /**
     * Holds the request if form was submitted
     *
     * @var array Request
     */
    protected static $request = [];

    /**
     * Holds all fields that should be added by sbnc
     *
     * @var array Fields
     */
    protected static $fields = [];

    /**
     * Holds all JavaScript that should be added
     *
     * @var array JavaScript
     */
    protected static $javascript = [];

    /**
     * Holds all options
     *
     * @var array Options
     */
    protected static $options = [];

    /**
     * Holds all error messages
     *
     * @var array Errors
     */
    protected static $errors = [];

    /**
     * Additional data that may be added
     *
     * @var array Data
     */
    protected static $data = [];

    public function __construct(array $data)
    {
        self::$components = [
            'modules' => $data['modules'],
            'addons' => $data['addons'],
            'utils' => $data['utils']
        ];
        self::$options = $data['options'];
    }

    public function __destruct()
    {
        if(ob_get_level() > 0) ob_flush();
    }

    /**
     * Handles static class calls to the Sbnc class:
     *
     * - Sbnc::errors()
     *    - no parameters
     *        return whole errors array
     *    - one parameter (int)
     *        return specific error by numeric index
     *
     * - Sbnc::request()
     *   - no parameters
     *         return whole request array
     *   - one parameter (string)
     *         return specific request by name (e.g. email field)
     *
     * - Sbnc::field() / Sbnc::fields()
     *   - no parameter
     *         return array with all fields
     *   - one parameter (string)
     *         return specific field by name (e.g. email field)
     *
     * - Sbnc::option() / Sbnc::options()
     *   - no parameter
     *         return array with all options
     *   - one parameter
     *         return specific option by key
     *
     * - Sbnc::data()
     *   - no parameter
     *         returns data array
     *   - 1 - 4 parameters
     *         return specific value by key(s)
     *
     * - Sbnc::module() / Sbnc::addon() / Sbnc::util()
     *   - no parameter
     *         return array with instances of all components of type
     *   - one parameter (string)
     *         return specific component instance by name
     *
     * - Other calls e.g. Sbnc::add_error('Custom error')
     *   - no parameter
     *         calls a public method of core with no parameters if possible
     *   - 1 - 4 parameters
     *         calls a public method of core with 1 - 4 parameter(s) if possible
     *
     * @param string $name Name of called method
     * @param string $params Parameters of called method
     * @return mixed Expected value or null
     * @throws \Exception If a method could not be called as expected
     */
    public function call($name, $params = '')
    {
        $count = 0;
        $first = $params;
        if (is_array($params)) {
            $count = count($params);
            if ($count == 0) {
                $first = '';
            } else {
                $first = $params[0];
            }
        }

        switch ($name) {
            case 'errors';
                if (empty($params)) {
                    if (isset(self::$errors)) {
                        return self::$errors;
                    }
                } elseif (isset(self::$errors[$first])) {
                    return self::$errors[$first];
                }
                return null;
            case 'request';
                if (empty($params)) {
                    if (isset(self::$request)) {
                        return self::$request;
                    }
                } elseif (isset(self::$request[$first])) {
                    return self::$request[$first];
                }
                return null;
            case 'field':
            case 'fields':
                if (empty($params)) {
                    if (isset(self::$fields)) {
                        return self::$fields;
                    }
                } elseif (isset(self::$fields[$first])) {
                    return self::$fields[$first];
                }
                return null;
            case 'option':
            case 'options':
                if (empty($params)) {
                    if (isset(self::$options)) {
                        return self::$options;
                    }
                } elseif (isset(self::$options[$first])) {
                    return self::$options[$first];
                }
                return null;
            case 'data':
                if ($count == 0) {
                    return self::$data;
                } elseif ($count == 1) {
                    if (isset(self::$data[$first])) {
                        return self::$data[$first];
                    }
                } elseif ($count == 2) {
                    if (isset(self::$data[$first][$params[1]])) {
                        return self::$data[$first][$params[1]];
                    }
                } elseif ($count == 3) {
                    if (isset(self::$data[$first][$params[1]][$params[2]])) {
                        return self::$data[$first][$params[1]][$params[2]];
                    }
                } elseif ($count == 4) {
                    if (isset(self::$data[$first][$params[1]][$params[2]][$params[3]])) {
                        return self::$data[$first][$params[1]][$params[2]][$params[3]];
                    }
                }
                return null;
            case 'module':
            case 'addon':
            case 'util':
                if (empty($params)) {
                    if (isset(self::$components[$name . 's'])) {
                        return self::$components[$name . 's'];
                    }
                } elseif (isset(self::$components[$name . 's'][$first])) {
                    return self::$components[$name . 's'][$first];
                }
                return null;
            default:
                $sbnc = Sbnc::core(); // only make public methods accessible
                if (!method_exists($sbnc, $name)) throw new \Exception('Method "' . $name . '" does not exist');
                if (!is_callable([$sbnc, $name])) throw new \Exception('Method "' . $name . '" could not be called');
                switch ($count) {
                    case 0:
                        return $sbnc->$name();
                    case 1:
                        return $sbnc->$name($params[0]);
                    case 2:
                        return $sbnc->$name($params[0], $params[1]);
                    case 3:
                        return $sbnc->$name($params[0], $params[1], $params[2]);
                    case 4:
                        return $sbnc->$name($params[0], $params[1], $params[2], $params[3]);
                    default:
                        return null;
                }
        }
    }

    /**
     * Initializes in correct order
     */
    public function init()
    {
        $this->init_javascript();
        $this->init_fields();
        $this->init_component('utils');
        $this->init_component('modules');
        $this->init_component('addons');
    }

    /**
     * Initializes form fields and adds required ones
     */
    private function init_fields()
    {
        if (strcmp(self::$options['prefix'][0], 'random') === 0) {
            self::$options['prefix'][0] = chr(rand(97, 122)) . substr(md5(microtime()), rand(0, 26), 4);
        }

        self::$fields = [
            'prefix' => &self::$options['prefix'][0],
            'url' => 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://') .
                $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ];
    }

    /**
     * Initializes all components by type and creates instances
     *
     * @param string $name Initializes modules, utils or addons
     */
    private function init_component($name)
    {
        self::$components[$name] = array_fill_keys(self::$components[$name], null);
        foreach (self::$components[$name] as $key => $value) {
            $class = __NAMESPACE__ . '\\' . $name . '\\' . $key;
            try {
                self::$components[$name][$key] = new $class();
            } catch (\Exception $e) {
                Sbnc::print_exception($e);
            }

        }
    }

    /**
     * Adds default JavaScript
     */
    private function init_javascript()
    {
        $lang = !self::$options['html5'] ? ' language="javascript" type="text/javascript"' : '';
        self::$javascript['_before_'] = '<script' . $lang . '>var sbnc = sbnc || {};';
        self::$javascript['_core_'] = 'sbnc.core = (function() {';
        self::$javascript['_core_'] .= 'var init; init = function() {};';
        self::$javascript['_core_'] .= 'return { init: init }; }()); sbnc.core.init();';
        self::$javascript['_after_'] = '</script>';
        self::$javascript['_modules_'] = [];
    }

    /**
     * Starts sbnc and performs all checks from loaded modules if enabled.
     * Only POST submits are handled!
     * Before and after actions are performed on every request.
     *
     * @param null $action User defined function called - if provided - after all spam checks
     * @return bool True if method was POST, false otherwise
     */
    public function start($action = null)
    {
        $this->before();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            $this->after();
            return false;
        }

        $this->manipulate_request();

        foreach (self::$components['modules'] as $module) {
            if ($module->is_enabled()) $module->check();
        }

        if (is_callable($action)) {
            $action();
        }

        $this->after();
        return true;
    }

    /**
     * Performs before actions from all components
     */
    private function before()
    {
        foreach (self::$components['utils'] as $util) $util->before();
        foreach (self::$components['modules'] as $module) $module->before();
        foreach (self::$components['addons'] as $addon) $addon->before();
    }

    /**
     * Performs after actions from all components
     */
    private function after()
    {
        foreach (self::$components['utils'] as $util) $util->after();
        foreach (self::$components['modules'] as $module) $module->after();
        foreach (self::$components['addons'] as $addon) $addon->after();
    }

    /**
     * Manipulates request to make it accessible in sbnc
     */
    private function manipulate_request()
    {
        $prefix = self::$options['prefix'][1];
        $random_prefix = isset($_POST[$prefix]) ? $_POST[$prefix] : '';
        $random_prefix_length = strlen($random_prefix);
        foreach ($_POST as $key => $value) {
            if (strcmp($key, $prefix) == 0) {
                self::$fields['prefix'] = $value;
            } elseif (strcmp(substr($key, 0, $random_prefix_length), $random_prefix) == 0) {
                self::$request[substr($key, $random_prefix_length)] = $value;
            } else {
                self::$request[$key] = $value;
            }
        }
    }

    /**
     * Returns true if form has been submitted and it's valid
     *
     * @return bool True if valid, false otherwise
     */
    public function is_valid()
    {
        if ($this->addon_exists('Flasher')) {
            $num_errors = $this->get_addon('Flasher')->count_errors();
            return $this->get_addon('Flasher')->was_submitted() && !($num_errors > 0);
        }
        return !(count(self::$errors) > 0) && strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
    }

    /**
     * Returns false if form has been submitted an errors occured
     *
     * @return bool True if invalid, false otherwise
     */
    public function is_invalid()
    {
        if ($this->addon_exists('Flasher')) {
            return !$this->is_valid() && $this->get_addon('Flasher')->was_submitted();
        } else {
            return !$this->is_valid() && strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
        }
    }

    /**
     * Checks if an addon has been loaded and is enabled
     *
     * @param string $addon Addon name
     * @return bool True if addon exists
     */
    protected function addon_exists($addon)
    {
        return array_key_exists($addon, self::$components['addons']) && self::$components['addons'][$addon]->is_enabled() ? true : false;
    }

    /**
     * Checks if a module has been loaded and is enabled
     *
     * @param string $module Module name
     * @return bool True if module exists
     */
    protected function module_exists($module)
    {
        return array_key_exists($module, self::$components['modules']) && self::$components['modules'][$module]->is_enabled() ? true : false;
    }

    /**
     * Checks if a util has been loaded and is enabled
     *
     * @param string $util Util name
     * @return bool True if util exists
     */
    protected function util_exists($util)
    {
        return array_key_exists($util, self::$components['utils']) && self::$components['utils'][$util]->is_enabled() ? true : false;
    }

    /**
     * Like util_exists but checks for any component
     *
     * @param string $component Component name
     * @return bool True if module, addon or util with this name exists
     */
    protected function component_exists($component)
    {
        return $this->addon_exists($component) || $this->module_exists($component) ? true : false ||
        $this->util_exists($component) ? true : false;
    }

    /**
     * Returns loaded addon instance by name
     *
     * @param string $addon Addon name
     * @return mixed Addon instance or null
     */
    public function get_addon($addon)
    {
        if ($this->addon_exists($addon)) return self::$components['addons'][$addon];
        return null;
    }

    /**
     * Returns loaded module instance by name
     *
     * @param string $module Module name
     * @return mixed Module instance or null
     */
    public function get_module($module)
    {
        if ($this->module_exists($module)) return self::$components['modules'][$module];
        return null;
    }

    /**
     * Returns loaded util instance by name
     *
     * @param string $util Util name
     * @return mixed Util instance or null
     */
    public function get_util($util)
    {
        if ($this->util_exists($util)) return self::$components['utils'][$util];
        return null;
    }

    /**
     * Retrieves a request (e.g. value of email field) by name from session or class
     *
     * @param string $key Input field name
     * @return string Value of sent input field
     */
    public function get_request($key)
    {
        if ($this->addon_exists('Flasher')) {
            return $this->get_addon('Flasher')->get_request($key);
        }
        return isset(self::$request[$key]) && !$this->is_valid() ? self::$request[$key] : '';
    }

    /**
     * Returns all errors from session or class
     *
     * @return array Errors
     */
    public function get_errors()
    {
        if ($this->addon_exists('Flasher')) {
            return $this->get_addon('Flasher')->get_errors();
        }
        return self::$errors;
    }

    /**
     * Returns a single error from session or class
     *
     * @return string Error
     */
    public function get_error()
    {
        if ($this->addon_exists('Flasher')) {
            $errors = $this->get_addon('Flasher')->get_errors();
            return reset($errors);
        }
        return reset(self::$errors);
    }

    public function get_value($name, $nl2br = false, $safe = false)
    {
        return $this->filter($name, $nl2br, $safe);
    }

    /**
     * Generates html code from all sbnc fields
     *
     * @return string HTML of input fields
     */
    public function get_fields()
    {
        $html = '';
        $tag_end = (self::$options['html5']) ? '' : '/';

        foreach (self::$fields as $key => $value) {
            $val = $value !== null ? $value : '';
            $id = strcmp($key, 'prefix') !== 0 ? self::$options['prefix'][0] . $key : self::$options['prefix'][1];
            $html .= '<input type="text" id="' . $id . '" name="' . $id . '" value="' . $val . '" style="display:none" ' . $tag_end . '>' . "\n";
        }

        return $html;
    }

    /**
     * Generates javascript code to be included
     *
     * @return string JavaScript code
     */
    public function get_js()
    {
        $js = '';
        $js .= self::$javascript['_before_'];
        $js .= self::$javascript['_core_'];
        foreach (self::$javascript['_modules_'] as $code) $js .= $code;
        $js .= self::$javascript['_after_'];
        return $js;
    }

    /**
     * Adds data to the $data array under a namespace
     *
     * @param string $namespace Namespace for data set
     * @param string $name Name (key) for data
     * @param string $value Value
     */
    public function add_data($namespace, $name, $value)
    {
        self::$data[$namespace][$name] = $value;
    }

    /**
     * Adds a field that will be included
     *
     * @param string $name Name of input field
     * @param string $value Default value
     */
    public function add_field($name, $value)
    {
        self::$fields[$name] = $value;
    }

    /**
     * Adds javascript code
     *
     * @param string $code Adds JavaScript
     */
    public function add_javascript($code)
    {
        array_push(self::$javascript['_modules_'], $code);
    }

    /**
     * Adds an error message
     *
     * @param string $error Error message
     * @return bool True if has been added
     */
    public function add_error($error)
    {
        if (is_string($error)) {
            array_push(self::$errors, $error);
            return true;
        }
        return false;
    }

    /**
     * Number of error messages in errors array
     *
     * @return int Number of errors
     */
    public function num_errors()
    {
        if ($this->addon_exists('Flasher')) {
            return $this->get_addon('Flasher')->count_errors();
        }
        return count(self::$errors);
    }

    /**
     * Echo's all errors in an unordered list
     *
     * @param string $class Class for HTML unordered list
     */
    public function print_errors($class = '')
    {
        echo empty($class) ? '<ul>' : '<ul class="' . $class . '>';
        foreach ($this->get_errors() as $key => $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
    }

    /**
     * Prints a single error
     */
    public function print_error()
    {
        echo $this->get_error();
    }

    /**
     * Prints a value.
     * use it to pre-fill forms, if errors occurred.
     *
     * @param string $name Name of input field
     * @param bool $nl2br Convert line breaks to HTML <br>
     */
    public function print_value($name, $nl2br = false)
    {
        echo $this->get_value($name, $nl2br);
    }

    /**
     * Prints html code of all fields
     */
    public function print_fields()
    {
        echo $this->get_fields();
    }

    /**
     * Prints JavaScript code
     */
    public function print_js()
    {
        echo $this->get_js();
    }

    /**
     * Checks if value is empty
     *
     * @param string $value Value to check
     * @return bool True if value is empty
     */
    protected function is_empty($value)
    {
        return (strlen(trim($value)) == 0);
    }

    /**
     * Filters a request by name so it's safe to print it in a html page.
     * Optionally converts line breaks to <br>
     *
     * @param string $key Request field name
     * @param bool $nl2br Convert line breaks to HTML <br>
     * @return string Filtered value
     */
    protected function filter($key, $nl2br = false)
    {
        $value = $this->get_request($key);
        if ($nl2br) {
            return !$this->is_empty($value) ? nl2br(htmlspecialchars($value, ENT_QUOTES)) : '';
        } else {
            return !$this->is_empty($value) ? htmlspecialchars($value, ENT_QUOTES) : '';
        }
    }

}
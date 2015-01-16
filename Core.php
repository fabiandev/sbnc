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
        if (ob_get_level() > 0) ob_flush();
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
     * - Other calls e.g. Sbnc::addError('Custom error')
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

    public function log($type, $msg)
    {
        if ($this->utilExists('LogMessages')) {
            $this->getUtil('LogMessages')->log($type, $msg);
        }
    }

    /**
     * Initializes in correct order
     */
    public function init()
    {
        $this->initComponents('utils');

        if (strcmp(self::$options['prefix'][0], 'random') === 0) {
            self::$options['prefix'][0] = chr(rand(97, 122)) . substr(md5(microtime()), rand(0, 26), 4);
        }

        $flash_enabled = $this->utilExists('FlashMessages');
        if ($flash_enabled) {
            $flash = $this->getUtil('FlashMessages');

            $flash->flash('core', 'prefix_fallback', $flash->getSafe('core', 'prefix_current'));
            $flash->flash('core', 'prefix_current', self::$options['prefix'][0]);
            $flash->flash('core', 'req_type_prev', $flash->getSafe('core', 'req_type'));

            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
                $flash->flash('core', 'prefix', self::$options['prefix'][0]);
                $flash->flash('core', 'req_type', 'get');
            } else {
                $flash->flash('core', 'req_type', 'post');
            }
        }

        $this->initJavascript();
        $this->initFields();
        $this->initComponents('modules');
        $this->initComponents('addons');

        if ($flash_enabled && isset(self::$fields['prefix'])) unset(self::$fields['prefix']);
    }

    /**
     * Initializes form fields and adds required ones
     */
    private function initFields()
    {
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
    private function initComponents($name)
    {
        self::$components[$name] = array_fill_keys(self::$components[$name], null);
        foreach (self::$components[$name] as $key => $value) {
            $class = __NAMESPACE__ . '\\' . $name . '\\' . $key;
            try {
                self::$components[$name][$key] = new $class();
            } catch (\Exception $e) {
                Sbnc::printException($e);
            }

        }
    }

    /**
     * Adds default JavaScript
     */
    private function initJavascript()
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

        $this->manipulateRequest();

        foreach (self::$components['modules'] as $module) {
            if ($module->isEnabled()) $module->check();
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
    private function manipulateRequest()
    {
        if ($this->utilExists('FlashMessages')) {
            $flashed = true;
        }

        $prefix = self::$options['prefix'][1];

        $random_prefix = $this->getPrefix();

        $random_prefix_length = strlen($random_prefix);

        foreach ($_POST as $key => $value) {
            if (!isset($flashed) && strcmp($key, $prefix) == 0) {
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
    public function isValid()
    {
        if ($this->submitted()) {
            if ($this->addonExists('Flasher')) {
                $flasher = $this->getAddon('Flasher');
                $num_errors = $flasher->countErrors();
                return !($num_errors > 0);
            }
            return !(count(self::$errors) > 0);
        }
        return false;
    }

    /**
     * Returns false if form has been submitted an errors occured
     *
     * @return bool True if invalid, false otherwise
     */
    public function isInvalid()
    {
        if ($this->submitted()) {
            return !$this->isValid();
        }
        return false;
    }

    public function passed() {
        return $this->isValid();
    }

    public function failed() {
        return $this->isInvalid();
    }

    /*public function submitted()
    {
        if ($this->addonExists('Flasher')) {
            return $this->getAddon('Flasher')->wasSubmitted();
        } else {
            return strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
        }
    }*/

    protected function submitted()
    {
        if ($this->utilExists('FlashMessages')) {
            $flash = $this->getUtil('FlashMessages');
            if ($flash->get('core', 'req_type_prev') == 'post') {
                return true;
            }
            return false;
        }
        return strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
    }

    /**
     * Checks if an addon has been loaded and is enabled
     *
     * @param string $addon Addon name
     * @return bool True if addon exists
     */
    protected function addonExists($addon, $check_enabled = true)
    {
        if (array_key_exists($addon, self::$components['addons'])) {
            if ($check_enabled) {
                if (self::$components['addons'][$addon]->isEnabled()) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Checks if a module has been loaded and is enabled
     *
     * @param string $module Module name
     * @return bool True if module exists
     */
    protected function moduleExists($module, $check_enabled = true)
    {
        if (array_key_exists($module, self::$components['modules'])) {
            if ($check_enabled) {
                if (self::$components['modules'][$module]->isEnabled()) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Checks if a util has been loaded and is enabled
     *
     * @param string $util Util name
     * @return bool True if util exists
     */
    protected function utilExists($util, $check_enabled = true)
    {
        if (array_key_exists($util, self::$components['utils'])) {
            if ($check_enabled) {
                if (self::$components['utils'][$util]->isEnabled()) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns loaded addon instance by name
     *
     * @param string $addon Addon name
     * @return mixed Addon instance or null
     */
    public function getAddon($addon)
    {
        if (!isset(self::$components['addons'][$addon])) {
            Sbnc::throwException('Addon "' . $addon . '" does not exist');
        }
        return self::$components['addons'][$addon];
    }

    /**
     * Returns loaded module instance by name
     *
     * @param string $module Module name
     * @return mixed Module instance or null
     */
    public function getModule($module)
    {
        if (!isset(self::$components['modules'][$module])) {
            Sbnc::throwException('Module "' . $module . '" does not exist');
        }
        return self::$components['modules'][$module];
    }

    /**
     * Returns loaded util instance by name
     *
     * @param string $util Util name
     * @return mixed Util instance or null
     */
    public function getUtil($util)
    {
        if (!isset(self::$components['utils'][$util])) {
            Sbnc::throwException('Util "' . $util . '" does not exist');
        }
        return self::$components['utils'][$util];
    }

    /**
     * @return string Return correct prefix to use to access forms
     */
    public function getPrefix()
    {
        if ($this->utilExists('FlashMessages')) {
            $flash = $this->getUtil('FlashMessages');

            $req_type = $flash->getSafe('core', 'req_type');
            $req_type_prev = $flash->getSafe('core', 'req_type_prev');

            if ($req_type == 'post' && ($req_type == $req_type_prev)) {
                return $flash->getSafe('core', 'prefix_fallback');
            } else {
                return $this->getSecretPrefix();
            }
        }
        return $this->getSecretPrefix();
    }


    /**
     * @return string Prefix from previous request
     */
    public function getSecretPrefix()
    {
        if ($this->utilExists('FlashMessages')) {
            $prefix = $this->getUtil('FlashMessages')->getSafe('core', 'prefix');
            return $prefix;
        } else {
            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
                return $_POST[self::$options['prefix'][1]];
            }
        }
        return self::$options['prefix'][0];
    }

    /**
     * @return string Get current prefix
     */
    public function getCurrentPrefix()
    {
        if ($this->utilExists('FlashMessages')) {
            $prefix = $this->getUtil('FlashMessages')->getSafe('core', 'prefix_current');
            return $prefix;
        } else {
            if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
                return $_POST[self::$options['prefix'][1]];
            }
        }
        return self::$options['prefix'][0];
    }

    /**
     * Retrieves a request (e.g. value of email field) by name from session or class
     *
     * @param string $key Input field name
     * @return string Value of sent input field
     */
    public function getRequest($key)
    {
        if ($this->addonExists('Flasher')) {
            return $this->getAddon('Flasher')->getRequest($key);
        }
        return isset(self::$request[$key]) && !$this->isValid() ? self::$request[$key] : '';
    }

    /**
     * Returns all errors from session or class
     *
     * @return array Errors
     */
    public function getErrors()
    {
        if ($this->addonExists('Flasher')) {
            $errors = $this->getAddon('Flasher')->getErrors();
            if (is_array($errors)) {
                return $errors;
            } else {
                return [];
            }
        }
        return self::$errors;
    }

    /**
     * Returns a single error from session or class
     *
     * @return string Error
     */
    public function getError()
    {
        $errors = $this->getErrors();
        return reset($errors);
    }

    public function getValue($name, $nl2br = false, $safe = false)
    {
        return $this->filter($name, $nl2br, $safe);
    }

    /**
     * Generates html code from all sbnc fields
     *
     * @return string HTML of input fields
     */
    public function getFields()
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
    public function getJavascript()
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
    public function addData($namespace, $name, $value)
    {
        self::$data[$namespace][$name] = $value;
    }

    /**
     * Adds a field that will be included
     *
     * @param string $name Name of input field
     * @param string $value Default value
     */
    public function addField($name, $value)
    {
        self::$fields[$name] = $value;
    }

    /**
     * Adds javascript code
     *
     * @param string $code Adds JavaScript
     */
    public function addJavascript($code)
    {
        array_push(self::$javascript['_modules_'], $code);
    }

    /**
     * Adds an error message
     *
     * @param string $error Error message
     * @return bool True if has been added
     */
    public function addError($error)
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
    public function numErrors()
    {
        if ($this->addonExists('Flasher')) {
            return count($this->getAddon('Flasher')->getErrors());
        }
        return count(self::$errors);
    }

    /**
     * Echo's all errors in an unordered list
     *
     * @param string $class Class for HTML unordered list
     */
    public function printErrors($class = '')
    {
        echo empty($class) ? '<ul>' : '<ul class="' . $class . '>';
        foreach ($this->getErrors() as $key => $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
    }

    /**
     * Prints a single error
     */
    public function printError()
    {
        echo $this->getError();
    }

    /**
     * Prints a value.
     * use it to pre-fill forms, if errors occurred.
     *
     * @param string $name Name of input field
     * @param bool $nl2br Convert line breaks to HTML <br>
     */
    public function printValue($name, $nl2br = false)
    {
        echo $this->getValue($name, $nl2br);
    }

    /**
     * Prints html code of all fields
     */
    public function printFields()
    {
        echo $this->getFields();
    }

    /**
     * Prints JavaScript code
     */
    public function printJavascript()
    {
        echo $this->getJavascript();
    }

    /**
     * Checks if value is empty
     *
     * @param string $value Value to check
     * @return bool True if value is empty
     */
    protected function isEmpty($value)
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
        $value = $this->getRequest($key);
        if ($nl2br) {
            return !$this->isEmpty($value) ? nl2br(htmlspecialchars($value, ENT_QUOTES)) : '';
        } else {
            return !$this->isEmpty($value) ? htmlspecialchars($value, ENT_QUOTES) : '';
        }
    }

}
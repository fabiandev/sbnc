<?php
namespace sbnc\core;

use sbnc\Sbnc;
use sbnc\helpers\Helpers;

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
        // if (ob_get_level() > 0) ob_flush();
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
            case 'modules':
            case 'addon':
            case 'addons':
            case 'util':
            case 'utils':
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

    ############################################
    #######    RESERVED METHOD NAMES!    #######
    ############################################
                                         #######
    private function errors() {}         #######
    private function request() {}        #######
    private function field() {}          #######
    private function fields() {}         #######
    private function option() {}         #######
    private function options() {}        #######
    private function data() {}           #######
    private function module() {}         #######
    private function addon() {}          #######
    private function util() {}           #######
                                         #######
    ############################################
    ############################################

    /**
     * Logs something to the log file
     *
     * @param string $type Title
     * @param mixed $msg Log message (string or array)
     */
    public function log($type, $msg)
    {
        if ($this->utilExists('LogMessages')) {
            $this->getUtil('LogMessages')->log($type, $msg);
        }
    }

    /**
     * Get an array of all log messages
     *
     * @return mixed false or array with log messages
     */
    public function getLog($reverse = true) {
        if ($this->utilExists('LogMessages')) {
            return $this->getUtil('LogMessages')->getLog($reverse);
        }
        return false;
    }

    /**
     * Initializes in correct order
     */
    public function init()
    {
        $this->initComponents('utils');

        if (strcmp(self::$options['prefix']['value'], 'random') === 0) {
            self::$options['prefix']['value'] = Helpers::randomKey(3, 6);
        }

        if ($this->utilExists('FlashMessages')) {
            $flash = $this->getUtil('FlashMessages');

            $flash->flash('core', 'req_type_prev', $flash->getSafe('core', 'req_type'));
            $flash->flash('core', 'req_type', Helpers::requestMethod(true));
        }

        $this->initJavascript();
        $this->initFields();
        $this->initComponents('modules');
        $this->initComponents('addons');
    }

    /**
     * Initializes form fields and adds required ones
     */
    private function initFields()
    {
        $this->addField(self::$options['prefix']['key'], self::$options['prefix']['value'], false);
        $this->addField('url', Helpers::getUrl());
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
            $class = 'sbnc\\' . $name . '\\' . $key;
            try {
                $c = self::$components[$name][$key] = new $class();
                $c->init();
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

        if (!Helpers::isPost()) {
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
        foreach (self::$components['utils'] as $util) if ($util->isEnabled()) $util->before();
        foreach (self::$components['modules'] as $module) if ($module->isEnabled()) $module->before();
        foreach (self::$components['addons'] as $addon) if ($addon->isEnabled()) $addon->before();
    }

    /**
     * Performs after actions from all components
     */
    private function after()
    {
        foreach (self::$components['utils'] as $util) if ($util->isEnabled()) $util->after();
        foreach (self::$components['modules'] as $module) if ($module->isEnabled()) $module->after();
        foreach (self::$components['addons'] as $addon) if ($addon->isEnabled()) $addon->after();

        if ($this->utilExists('FlashMessages') && strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            $this->getUtil('FlashMessages')->remove('core', 'redirected');
        }
    }

    /**
     * Manipulates request to make it accessible in sbnc
     */
    private function manipulateRequest()
    {
        if ($this->utilExists('FlashMessages')) {
            $flashed = true;
        }

        $prefix_field = self::$options['prefix']['key'];
        $random_prefix = $this->getSecretPrefix();
        $random_prefix_length = strlen($random_prefix);

        foreach ($_POST as $key => $value) {
            $decoded_key = str_rot13($key);
            if (!isset($flashed) && strcmp($decoded_key, $prefix_field) == 0) {
                self::$options['prefix']['secret'] = $value;
            } elseif (strcmp(substr($key, 0, $random_prefix_length), $random_prefix) == 0) {
                self::$request[substr($decoded_key, $random_prefix_length)] = $value;
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

    public function submitted()
    {
        if ($this->utilExists('FlashMessages')) {
            $flash = $this->getUtil('FlashMessages');

            $prev = $flash->getSafe('core', 'req_type_prev');
            $curr = $flash->getSafe('core', 'req_type');
            $re = $flash->get('core', 'redirected');

            if (($prev == 'post' && $re) || $curr == 'post') {
                return true;
            }
            return false;
        }
        return Helpers::isPost();
    }

    public function redirect()
    {
        if (!headers_sent()) {
            if ($this->utilExists('FlashMessages')) {
                $this->getUtil('FlashMessages')->flash('core', 'redirected', true);
            }
            header('Location: ' . Sbnc::request('url'));
            exit;
        } else {
            $msg = 'Headers have already been sent. Make sure to include sbnc before any other output';
            Sbnc::printException(new \Exception($msg));
        }
    }

    /**
     * Checks if a component has been loaded and is enabled
     *
     * @param string $component Component name
     * @return bool True if addon exists
     */
    public function componentExists($type, $component, $check_enabled = true)
    {
        if (array_key_exists($component, self::$components[$type])) {
            if ($check_enabled) {
                if (self::$components[$type][$component]->isEnabled()) {
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
     * Checks if an addon has been loaded and is enabled
     *
     * @param string $addon Addon name
     * @return bool True if addon exists
     */
    public function addonExists($addon, $check_enabled = true)
    {
        return $this->componentExists('addons', $addon, $check_enabled);
    }

    /**
     * Checks if a module has been loaded and is enabled
     *
     * @param string $module Module name
     * @return bool True if module exists
     */
    public function moduleExists($module, $check_enabled = true)
    {
        return $this->componentExists('modules', $module, $check_enabled);
    }

    /**
     * Checks if a util has been loaded and is enabled
     *
     * @param string $util Util name
     * @return bool True if util exists
     */
    public function utilExists($util, $check_enabled = true)
    {
        return $this->componentExists('utils', $util, $check_enabled);
    }

    /**
     * Returns loaded component instance by name and type
     *
     * @param string $addon Component name
     * @return mixed Component instance or null
     */
    public function getComponent($type, $component, $check_enabled = true)
    {
        if (!$this->componentExists($type, $component, $check_enabled)) {
            return null;
            // Sbnc::throwException(ucfirst(rtrim($type, 's')) . '"' . $component . '" does not exist');
        }
        return self::$components[$type][$component];
    }

    /**
     * Returns loaded addon instance by name
     *
     * @param string $addon Addon name
     * @return mixed Addon instance or null
     */
    public function getAddon($addon, $check_enabled = true)
    {
        return $this->getComponent('addons', $addon, $check_enabled);
    }

    /**
     * Returns loaded module instance by name
     *
     * @param string $module Module name
     * @return mixed Module instance or null
     */
    public function getModule($module, $check_enabled = true)
    {
        return $this->getComponent('modules', $module, $check_enabled);
    }

    /**
     * Returns loaded util instance by name
     *
     * @param string $util Util name
     * @return mixed Util instance or null
     */
    public function getUtil($util, $check_enabled = true)
    {
        return $this->getComponent('utils', $util, $check_enabled);
    }

    /**
     * Generates field name from string that would be
     * used in the form by adding it via addField()
     *
     * @return string Used field name
     */
    public function getFieldName($name)
    {
        return $this->getCurrentPrefix() . str_rot13($name);
    }

    /**
     * @return string Prefix from previous request
     */
    public function getSecretPrefix()
    {
        if (Helpers::isPost()) {
            return $_POST[str_rot13(self::$options['prefix']['key'])];
        }

        return null;
    }

    /**
     * @return string Get current prefix
     */
    public function getCurrentPrefix()
    {
        return self::$options['prefix']['value'];
    }

    /**
     * @return string Return correct prefix to use to access forms
     */
    public function getCurrentPrefixKey()
    {
        return self::$options['prefix']['key'];
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

    public function getValue($name, $nl2br = false)
    {
        $value = $this->getRequest($name);
        return Helpers::filter($value, $nl2br);
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
            $id = $key;

            $html .= '<input type="text" id="' . $id;
            $html .= '" name="' . $id . '" value="';
            $html .= $val . '" style="display:none" ';
            $html .= $tag_end . '>' . "\n";
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
    public function addField($name, $value, $prefix = true)
    {
        $val = str_rot13($name);
        $key = $prefix ? $this->getCurrentPrefix() . $val : $val;
        self::$fields[$key] = $value;
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

}

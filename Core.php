<?php
namespace sbnc;

class Core
{
    protected static $components = [];
    protected static $data       = [];
    protected static $options    = [];
    protected static $errors     = [];

    /*protected $fields = [];
    protected $request = [];
    protected $errors = [];
    protected $master = [];*/

    public function __construct(array $data) {
        self::$components = [
            'modules' => $data['modules'],
            'addons'  => $data['addons'],
            'utils'   => $data['utils']
        ];
        self::$options = $data['options'];
        $this->init();
    }


    public static function call($func, array &$params = array()) {
        list($class, $method) = $func;
        $instance = is_object($class);

        switch (count($params)) {
            case 0:
                return ($instance) ?
                    $class->$method() :
                    $class::$method();
            case 1:
                return ($instance) ?
                    $class->$method($params[0]) :
                    $class::$method($params[0]);
            case 2:
                return ($instance) ?
                    $class->$method($params[0], $params[1]) :
                    $class::$method($params[0], $params[1]);
            case 3:
                return ($instance) ?
                    $class->$method($params[0], $params[1], $params[2]) :
                    $class::$method($params[0], $params[1], $params[2]);
            case 4:
                return ($instance) ?
                    $class->$method($params[0], $params[1], $params[2], $params[3]) :
                    $class::$method($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return ($instance) ?
                    $class->$method($params[0], $params[1], $params[2], $params[3], $params[4]) :
                    $class::$method($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                return call_user_func_array($func, $params);
        }
    }


    private function init() {
        $this->init_fields();
        $this->init_utils();
        $this->init_modules();
        $this->init_addons();
    }

    private function init_fields() {
        if (strcmp(self::$options['prefix'][0], 'random') === 0) {
            self::$options['prefix'][0] = chr(rand(97,122)).substr(md5(microtime()),rand(0,26),4);
        }

        $this->fields = [
            'js'     => null,
            'prefix' => &self::$options['prefix'][0],
            'url'    => 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://') .
                $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ];
    }

    private function init_utils() {
        self::$components['utils'] = array_fill_keys(self::$components['utils'], null);
        foreach (self::$components['utils'] as $key => $value) {
            $class = __NAMESPACE__ . '\\utils\\' . $key;
            self::$components['utils'][$key] = new $class();
        }
    }

    private function init_modules() {
        self::$components['modules'] = array_fill_keys(self::$components['modules'], null);
        foreach (self::$components['modules'] as $key => $value) {
            $class = __NAMESPACE__ . '\\modules\\' . $key;
            self::$components['modules'] = new $class();
        }
    }

    private function init_addons() {
        self::$components['addons'] = array_fill_keys(self::$components['addons'], null);
        foreach (self::$components['addons'] as $key => $value) {
            $class = __NAMESPACE__ . '\\addons\\' . $key;
            self::$components['addons'][$key] = new $class();
        }
    }

    protected function addon_exists($addon) {
        return array_key_exists($addon, self::$components['addons']) && self::$components['addons'][$addon]->is_enabled() ? true : false;
    }

    protected function module_exists($module) {
        return array_key_exists($module, self::$components['modules']) && self::$components['modules'][$module]->is_enabled() ? true : false;
    }

    protected function util_exists($util) {
        return array_key_exists($util, self::$components['utils']) && self::$components['utils'][$util]->is_enabled() ? true : false;
    }

    protected function component_exists($component) {
        return $this->addon_exists($component) || $this->module_exists($component) ? true : false ||
               $this->util_exists($component) ? true : false;
    }

    public function get_addon($addon) {
        if ($this->addon_exists($addon)) return self::$components['addons'][$addon];
        return null;
    }

    public function get_module($module) {
        if ($this->module_exists($module)) return self::$components['modules'][$module];
        return null;
    }

    public function get_util($util) {
        if ($this->util_exists($util)) return self::$components['utils'][$util];
        return null;
    }

    protected function before() {
        foreach (self::$components['utils'] as $util) $util->before();
        foreach (self::$components['modules'] as $module) $module->before();
        foreach (self::$components['addons'] as $addon) $addon->before();
    }

    protected function after() {
        foreach (self::$components['utils'] as $util) $util->after();
        foreach (self::$components['modules'] as $module) $module->after();
        foreach (self::$components['addons'] as $addon) $addon->after();
    }

    protected function manipulate_request() {
        $prefix = self::$options['prefix'][1];
        $random_prefix = isset($_POST[$prefix]) ? $_POST[$prefix] : '';
        $random_prefix_length = strlen($random_prefix);
        foreach($_POST as $key => $value) {
            if (strcmp($key, $prefix) == 0) {
                self::$data['request']['prefix'] = $value;
            } elseif (strcmp(substr($key, 0, $random_prefix_length), $random_prefix) == 0) {
                self::$data['request'][substr($key, $random_prefix_length)] = $value;
            } else {
                self::$data['request'][$key] = $value;
            }
        }
    }

    protected function get_request($key, $safe = false) {
        if ($this->addon_exists('Flasher')) {
            return $this->get_addon('Flasher')->get_request($key, $safe);
        }
        return isset(self::$data['request'][$key]) && !$this->is_valid() ? self::$data['request'][$key] : '';
    }

    protected function is_empty($value) {
        return (strlen(trim($value)) == 0);
    }

    protected function filter($key, $nl2br = false) {
        $value = $this->get_request($key);
        if($nl2br) {
            return !$this->is_empty($value) ? nl2br(htmlspecialchars($value, ENT_QUOTES)) : '';
        } else {
            return !$this->is_empty($value) ? htmlspecialchars($value, ENT_QUOTES) : '';
        }
    }

    public function start($action = null) {
        echo 'start';
        exit;
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
            $action($this);
        }

        $this->after();
        return true;
    }

    public function is_valid() {
        if ($this->addon_exists('Flasher')) {
            $num_errors = $this->get_addon('Flasher')->count_errors();
            return $this->get_addon('Flasher')->was_submitted() && !($num_errors > 0);
        }
        return !(count(self::$errors) > 0) && strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
    }

    public function is_invalid() {
        if ($this->addon_exists('Flasher')) {
            return !$this->is_valid() && $this->get_addon('Flasher')->was_submitted();
        } else {
            return !$this->is_valid() && strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
        }
    }

    public function add_error($error) {
        if (is_string($error)) {
            array_push(self::$errors, $error);
            return true;
        }
        return false;
    }

    public function num_errors() {
        if ($this->addon_exists('Flasher')) {
            return $this->get_addon('Flasher')->count_errors();
        }
        return count(self::$errors);
    }

    public function get_errors() {
        if ($this->addon_exists('Flasher')) {
            return $this->get_addon('Flasher')->get_errors();
        }
        return self::$errors;
    }

    public function print_errors($class = '') {
        echo empty($class) ? '<ul>' : '<ul class="' . $class . '>';
        foreach ($this->get_errors() as $key => $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
    }

    public function get_error() {
        if ($this->addon_exists('Flasher')) {
            $errors = $this->get_addon('Flasher')->get_errors();
            return reset($errors);
        }
        return reset(self::$errors);
    }

    public function print_error() {
        echo $this->get_error();
    }

    public function get_value($name, $nl2br = false, $safe = false) {
        return $this->filter($name, $nl2br, $safe);
    }

    public function print_value($name, $nl2br = false, $safe = false) {
        echo $this->get_value($name, $nl2br, $safe);
    }

    public function print_fields() {
        echo $this->get_fields();
    }

    public function get_fields() {
        $html = '';
        $tag_end = (self::$options['html5']) ? '' : '/';

        foreach (self::$data['fields'] as $key => $value) {
            $val = $value !== null ? $value : '';
            $id = strcmp($key, 'prefix') !== 0 ? self::$options['prefix'][0].$key : self::$options['prefix'][1];
            $html .= '<input type="text" id="'.$id.'" name="'.$id.'" value="'.$val.'" style="display:none" '.$tag_end.'>'."\n";
        }

        return $html;
    }

    public function print_js() {
        echo $this->get_js();
    }

    public function get_js() {
        $lang = !self::$options['html5'] ? ' language="javascript" type="text/javascript"' : '';
        $prefix = self::$options['prefix'][0];
        $keyboard_field = $prefix . 'keyboard';
        $mouse_field = $prefix . 'mouse';
        $js_field = $prefix . 'js';

$code = <<<CODE
<script$lang>
var sbnc = sbnc || {};

sbnc.core = (function() {

    var init,
        usedKeyboard,
        usedMouse;

    var keyboardField,
        mouseField,
        jsField;

    init = function() {
        keyboardField = document.getElementById('$keyboard_field');
        mouseField    = document.getElementById('$mouse_field');
        jsField       = document.getElementById('$js_field');

        jsField.value = 'true';
        window.onkeyup     = usedKeyboard;
        window.onmousemove = usedMouse;
    };

    usedKeyboard = function() {
        keyboardField.value = 'true';
    }

    usedMouse = function() {
        mouseField.value = 'true';
    }

    return {
        init: init
    };

}());

sbnc.core.init();
</script>

CODE;
        return $code;
    }

}
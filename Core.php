<?php
namespace Sbnc;

class Core
{
    protected $fields = [];
    protected $request = [];
    protected $errors = [];
    protected $master = [];

    public function __construct() {
        $this->init();
    }

    private function init() {
        $this->init_fields();
        $this->init_master();
        $this->init_utils();
        $this->init_modules();
        $this->init_addons();
    }

    private function init_fields() {
        if (strcmp($this->options['prefix'][0], 'random') === 0) {
            $this->options['prefix'][0] = chr(rand(97,122)).substr(md5(microtime()),rand(0,26),4);
        }

        $this->fields = [
            'js'     => null,
            'prefix' => &$this->options['prefix'][0],
            'url'    => 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://') .
                $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ];
    }

    private function init_master() {
        $this->master = [
            'request'     => &$this->request,
            'errors'      => &$this->errors,
            'utils'       => &$this->utils,
            'modules'     => &$this->modules,
            'addons'      => &$this->addons,
            'options'     => &$this->options,
            'fields'      => &$this->fields,
        ];
    }

    private function init_utils() {
        $this->utils = array_fill_keys($this->utils, null);
        foreach ($this->utils as $key => $value) {
            $class = __NAMESPACE__ . '\\Utils\\' . $key;
            $this->utils[$key] = new $class();
        }
    }

    private function init_modules() {
        $this->modules = array_fill_keys($this->modules, null);
        foreach ($this->modules as $key => $value) {
            $class = __NAMESPACE__ . '\\Modules\\' . $key;
            $this->modules[$key] = new $class($this->master);
        }
    }

    private function init_addons() {
        $this->addons = array_fill_keys($this->addons, null);
        foreach ($this->addons as $key => $value) {
            $class = __NAMESPACE__ . '\\Addons\\' . $key;
            $this->addons[$key] = new $class($this->master);
        }
    }

    protected function addon_exists($addon) {
        return array_key_exists($addon, $this->addons) && $this->addons[$addon]->is_enabled() ? true : false;
    }

    protected function module_exists($module) {
        return array_key_exists($module, $this->modules) && $this->modules[$module]->is_enabled() ? true : false;
    }

    protected function util_exists($util) {
        return array_key_exists($util, $this->utils) && $this->utils[$util]->is_enabled() ? true : false;
    }

    protected function component_exists($component) {
        return $this->addon_exists($component) || $this->module_exists($component) ? true : false ||
               $this->util_exists($component) ? true : false;
    }

    public function get_addon($addon) {
        if ($this->addon_exists($addon)) {
            return $this->addons[$addon];
        }
        return null;
    }

    public function get_module($module) {
        if ($this->module_exists($module)) return $this->modules[$module];
        return null;
    }

    public function get_util($util) {
        if ($this->util_exists($util)) return $this->utils[$util];
        return null;
    }

    protected function before() {
        foreach ($this->utils as $util) $util->before();
        foreach ($this->modules as $module) $module->before();
        foreach ($this->addons as $addon) $addon->before();
    }

    protected function after() {
        foreach ($this->utils as $util) $util->after();
        foreach ($this->modules as $module) $module->after();
        foreach ($this->addons as $addon) $addon->after();
    }

    protected function manipulate_request() {
        $prefix = $this->options['prefix'][1];
        $random_prefix = isset($_POST[$prefix]) ? $_POST[$prefix] : '';
        $random_prefix_length = strlen($random_prefix);
        foreach($_POST as $key => $value) {
            if (strcmp($key, $prefix) == 0) {
                $this->request['prefix'] = $value;
            } elseif (strcmp(substr($key, 0, $random_prefix_length), $random_prefix) == 0) {
                $this->request[substr($key, $random_prefix_length)] = $value;
            } else {
                $this->request[$key] = $value;
            }
        }
    }

    protected function get_request($key, $safe = false) {
        if ($this->addon_exists('Flasher')) {
            return $this->addons['Flasher']->get_request($key, $safe);
        }
        return isset($this->request[$key]) && !$this->is_valid() ? $this->request[$key] : '';
    }

    protected function is_empty($value) {
        return (strlen(trim($value)) == 0);
    }

    protected function filter($key, $nl2br = false, $safe = false) {
        $value = $this->get_request($key, $safe);
        if($nl2br) {
            return !$this->is_empty($value) ? nl2br(htmlspecialchars($value, ENT_QUOTES)) : '';
        } else {
            return !$this->is_empty($value) ? htmlspecialchars($value, ENT_QUOTES) : '';
        }
    }

    public function start($action = null) {
        $this->before();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            $this->after();
            return false;
        }

        $this->manipulate_request();

        foreach ($this->modules as $module) {
            if ($module->is_enabled()) $module->check($this->master);
        }

        if (is_callable($action)) {
            $action($this);
        }

        $this->after();
        return true;
    }

    public function is_valid() {
        if ($this->addon_exists('Flasher')) {
            $num_errors = $this->addons['Flasher']->count_errors();
            return $this->addons['Flasher']->was_submitted() && !($num_errors > 0); // $this->utils['FlashMessages']->get('_sbnc', 'redirected')
        }
        return !(count($this->errors) > 0) && strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
    }

    public function is_invalid() {
        if ($this->addon_exists('Flasher')) {
            return !$this->is_valid() && $this->addons['Flasher']->was_submitted();
        } else {
            return !$this->is_valid() && strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
        }
    }

    public function add_error($error) {
        if (is_string($error)) {
            array_push($this->errors, $error);
            return true;
        }
        return false;
    }

    public function num_errors() {
        if ($this->addon_exists('Flasher')) {
            return $this->addons['Flasher']->count_errors();
        }
        return count($this->errors);
    }

    public function get_errors() {
        if ($this->addon_exists('Flasher')) {
            return $this->addons['Flasher']->get_errors();
        }
        return $this->errors;
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
            $errors = $this->addons['Flasher']->get_errors();
            return reset($errors);
        }
        return reset($this->errors);
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
        $tag_end = ($this->options['html5']) ? '' : '/';

        foreach ($this->fields as $key => $value) {
            $val = $value !== null ? $value : '';
            $id = strcmp($key, 'prefix') !== 0 ? $this->options['prefix'][0].$key : $this->options['prefix'][1];
            $html .= '<input type="text" id="'.$id.'" name="'.$id.'" value="'.$val.'" style="display:none" '.$tag_end.'>'."\n";
        }

        return $html;
    }

    public function print_js() {
        echo $this->get_js();
    }

    public function get_js() {
        $lang = !$this->options['html5'] ? ' language="javascript" type="text/javascript"' : '';
        $prefix = $this->options['prefix'][0];
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
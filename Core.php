<?
namespace Sbnc;

class Core
{
    protected $fields = [];

    protected $request = [];

    protected $errors = [];

    protected $master = [];

    public function __construct() {
        $this->utils = array_fill_keys($this->utils, null);
        $this->modules = array_fill_keys($this->modules, null);
        $this->addons = array_fill_keys($this->addons, null);
        $this->init();
    }

    protected function addon_exists($addon) {
        return array_key_exists($addon, $this->addons) ? true : false;
    }

    protected function module_exists($module) {
        return array_key_exists($module, $this->modules) ? true : false;
    }

    protected function component_exists($component) {
        return $this->addon_exists($component) || $this->module_exists($component) ? true : false;
    }

    protected function init() {
        $this->init_fields();
        $this->init_master();
        $this->init_utils();
        $this->init_modules();
        $this->init_addons();
    }

    protected function init_fields() {
        if (strcmp($this->options['prefix'][0], 'random') === 0) {
            $this->options['prefix'][0] = chr(rand(97,122)).substr(md5(microtime()),rand(0,26),4);
        }

        $this->fields = [
            'js'     => null,
            'count'  => 0,
            'prefix' => &$this->options['prefix'][0],
            'url'    => 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://') .
                $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ];
    }

    protected function init_master() {
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

    protected function init_modules() {
        foreach ($this->modules as $key => $value) {
            $class = __NAMESPACE__ . '\\Modules\\' . $key;
            $this->modules[$key] = new $class($this->master);
        }
    }

    protected function init_addons() {
        foreach ($this->addons as $key => $value) {
            $class = __NAMESPACE__ . '\\Addons\\' . $key;
            $this->addons[$key] = new $class($this->master);
        }
    }

    protected function init_utils() {
        foreach ($this->utils as $key => $value) {
            $class = __NAMESPACE__ . '\\Utils\\' . $key;
            $this->utils[$key] = new $class();
        }
    }

    protected function before() {
        foreach ($this->modules as $module) {
            $module->before();
        }
        foreach ($this->addons as $addon) {
            $addon->before();
        }
    }

    protected function after() {
        foreach ($this->modules as $module) {
            $module->after();
        }
        foreach ($this->addons as $addon) {
            $addon->after();
        }
    }

    public function start() {
        $this->before();
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            $this->after();
            return false;
        }

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

        foreach ($this->modules as $module) {
            $module->check($this->master);
        }
        $this->after();
        return true;
    }

    public function get_errors() {
        if ($this->addon_exists('Flasher')) {
            return $this->addons['Flasher']->get_errors();
        }
        return $this->errors;
    }

    public function is_valid() {
        if ($this->addon_exists('Flasher')) {
            $num_errors = $this->addons['Flasher']->count_errors();
            return !($num_errors > 0);
        }
        return !(count($this->errors) > 0);
    }

    public function is_invalid() {
        return !$this->is_valid();
    }

    public function print_errors($class = '') {
        if ($this->is_valid()) return;
        echo empty($class) ? '<ul>' : '<ul class="' . $class . '>';
        foreach ($this->get_errors() as $key => $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
    }

    public function get_flash_message($type, $key) {
        return $this->utils['FlashMessages']->get($type, $key);
    }

    public function print_flash_message($type, $key) {
        echo $this->get_message($type, $key);
    }

    public function get_flash_messages($type) {
        return $this->utils['FlashMessages']->get($type);
    }

    public function print_flash_messages($type) {
        echo empty($class) ? '<ul>' : '<ul class="' . $class . '>';
        foreach ($this->get_flash_messages($type) as $key => $message) {
            echo '<li>' . $message . '</li>';
        }
        echo '</ul>';
    }

    public function get_value($name, $nl2br = false) {
        return $this->filter($name, $nl2br);
    }

    public function print_value($name, $nl2br = false) {
        echo $this->get_value($name, $nl2br);
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

    public function print_fields() {
        echo $this->get_fields();
    }


    public function get_js() {
        return '';
    }

    public function print_js() {
        echo $this->get_js();
    }

    // helper functions

    private function get_request($key) {
        if ($this->addon_exists('Flasher')) {
            return $this->addons['Flasher']->get_request($key);
        }
        return isset($this->request[$key]) && !$this->is_valid() ? $this->request[$key] : '';
    }

    private function is_empty($value) {
        return (strlen(trim($value)) == 0);
    }

    public function filter($key, $nl2br = false) {
        $value = $this->get_request($key);
        if($nl2br) {
            return !$this->is_empty($value) ? nl2br(htmlspecialchars($value, ENT_QUOTES)) : '';
        } else {
            return !$this->is_empty($value) ? htmlspecialchars($value, ENT_QUOTES) : '';
        }
    }

}
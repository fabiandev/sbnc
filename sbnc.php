<?php
namespace Fw\Sbnc;
use Fw\Sbnc\Modules;

/*
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

    /*
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

    /*
     * Options for sbnc.
     *
     * @var array
     */
    private $options = [
        'prefix'      => 'random',
        'javascript'  => true,
        'html5'       => true
    ];

    private $fields = [];

    private $master = [];
    private $errors = [];
    private $request = null;

    public function __construct() {
        spl_autoload_register('load_modules');
        array_fill_keys($this->modules, null);
        $this->init();
    }

    private function load_modules($class) {
        include 'modules/' . $class . '.php';
    }

    private function init() {
        $this->fields = [
            'js'     => false,
            'prefix' => &$this->options['prefix']
        ];

        if (strcmp($this->options['prefix'], 'random') === 0) {
            $this->options['prefix'] = substr(md5(microtime()),rand(0,26),4) . '_';
        } else {
            $this->options['prefix'] = 'sbnc_';
        }

        $this->master = [
            'request'     => &$this->request,
            'modules'     => &$this->modules,
            'options'     => &$this->options,
            'fields'      => &$this->fields,
        ];

        $this->init_modules();
    }

    private function init_modules() {
        foreach ($this->mudules as $key => $value) {
            $this->modules[$key] = new $key($this->master);
        }
    }

    public function check($request) {
        $this->request = $request;
        foreach ($this->modules as $module) {
            $module->check($this->master);
        }
    }

    public function get_errors() {
        return $this->errors;
    }

    public function get_value($name) {

    }

    public function get_fields() {
        $html = '';
        $tag_end = ($this->options['html5']) ? '' : '/';

        foreach ($this->fields as $key => $value) {
            $val = $value !== false ? $value : '';
            $id = $this->options['prefix'].$key;
            $html .= '<input type="text" id="'.$id.'" name="'.$id.'" value="'.$val.'" style="display:none" '.$tag_end.'>\n';
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

}


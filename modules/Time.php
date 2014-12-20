<?php
namespace Sbnc\Modules;

/**
 * sbnc time module
 *
 * Checks how long it took, until the form was submitted.
 *
 * @package    Sbnc
 * @subpackage Modules
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.1
 * @link       https://github.com/fabianweb/sbnc/modules/
 */
class Time extends Module implements ModuleInterface {

    /**
     * Module options
     *
     * min: minimum time in seconds
     * max: maximum time in seconds
     *
     * @var array
     */
    private $options = [
        'min' => 1,
        'max' => 600
    ];

    private $errors = [
        'min' => 'Sorry, this was too fast.',
        'max' => 'Sorry, this took too long. Try again!'
    ];

    /**
     * Adds a field to sbnc
     *
     * @param $master
     */
    protected function init() {
        $this->master['fields']['time'] = time();
    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check() {
        $now = time();
        $time = $this->master['request']['time'];
        $diff = $now - $time;

        if ($diff < $this->options['min']) {
            array_push($this->master['errors'], $this->errors['min']);
        } elseif ($diff > $this->options['max']) {
            array_push($this->master['errors'], $this->errors['max']);
        }
    }

}
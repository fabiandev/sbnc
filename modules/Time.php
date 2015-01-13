<?php
namespace Sbnc\Modules;

class Time extends Module implements ModuleInterface {

    private $options = [
        'min' => 1,
        'max' => 600
    ];

    private $errors = [
        'min' => 'Sorry, this was too fast.',
        'max' => 'Sorry, this took too long. Try again!'
    ];

    protected function init() {
        $this->enabled = true;
        $this->master['fields']['time'] = time();
    }

    public function check() {
        $now = time();
        $time = $this->master['request']['time'];
        $diff = $now - $time;

        if ($diff < $this->options['min']) {
            array_push($this->master['errors'], $this->errors['min']);
            $this->master['utils']['LogMessages']->log('spam-fast-response', 'Submit too fast: < ' . $this->options['min']);
        } elseif ($diff > $this->options['max']) {
            array_push($this->master['errors'], $this->errors['max']);
            $this->master['utils']['LogMessages']->log('spam-timeout', 'Submit too slow: > ' . $this->options['max']);
        }
    }

}
<?php
namespace Sbnc\Modules;

class Hidden extends Module implements ModuleInterface {

    private $errors = [
        'error'     => 'Spam! %field% is not empty.'
    ];

    protected function init() {
        $this->enabled = true;
        $this->master['fields']['check'] = null;
    }

    public function check() {
        if (!isset($this->master['request']['check']) ||  strlen(trim($this->master['request']['check'])) != 0) {
            $err = str_replace('%field%', 'check', $this->errors['error']);
            array_push($this->master['errors'], $err);
            $this->master['utils']['LogMessages']->log('spam-hidden', 'Hidden field was not empty: ' . $this->master['request']['check']);
        }
    }

}
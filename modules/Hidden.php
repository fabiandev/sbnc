<?php
namespace sbnc\modules;
use sbnc\Sbnc;

class Hidden extends Module implements ModuleInterface {

    private $errors = [
        'error'     => 'Spam! %field% is not empty.'
    ];

    protected function init() {
        $this->enabled = true;
        Sbnc::add_field('check', null);
    }

    public function check() {
        $hidden_value = Sbnc::data(['request', 'check']);
        if ($hidden_value === null ||  strlen(trim($hidden_value)) != 0) {
            $err = str_replace('%field%', 'check', $this->errors['error']);
            Sbnc::add_error($err);
            Sbnc::util('LogMessages')->log('spam-hidden', 'Hidden field was not empty: ' . $hidden_value);
        }
    }

}
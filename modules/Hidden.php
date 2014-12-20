<?php
namespace Sbnc\Modules;

class Hidden extends Module implements ModuleInterface {

    protected function init() {
        $this->master['fields']['check'] = null;
    }

    public function check() {

    }

}
<?php
namespace Fw\Sbnc\Modules;

Interface module {

    public function __construct(&$master);
    public function check($master);

}

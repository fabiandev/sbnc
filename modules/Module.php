<?php
namespace Sbnc\Modules;

Interface Module {

    public function __construct(&$master);
    public function check($master);

}

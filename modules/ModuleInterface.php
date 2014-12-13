<?php
namespace Sbnc\Modules;

Interface ModuleInterface {

    public function __construct(&$master);
    public function check($master);

}

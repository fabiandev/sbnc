<?php
spl_autoload_register(function ($class) {
    $split = explode('\\', $class);
    $sub = $split[count($split)-2];
    if (strcasecmp('sbnc', $sub) === 0) {
        include end($split) . '.php';
    } else {
        include $sub . '/' . end($split) . '.php';
    }
});

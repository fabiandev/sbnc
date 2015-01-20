<?php
/**
 * class autoloader
 */
spl_autoload_register(function ($class) {
    $split = explode('\\', $class);
    $count = count($split);
    $path = '';
    for ($i = 0; $i < $count; $i++) {
        if ($i == 0 && $split[$i] == 'sbnc') continue;
        $path .= $split[$i] . '/';
    }
    $path = trim($path, '/') . '.php';
    require_once __DIR__ . '/' . $path;
});

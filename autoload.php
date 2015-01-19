<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

$module = new stdClass();

require_once __DIR__.'/version.php';

/**
 * register autoload function
 */
spl_autoload_register(
    function ($class) use ($module) {
        $classname = str_replace($module->component.'\\', '', $class);
        $file = __DIR__.DIRECTORY_SEPARATOR.
            'classes'.DIRECTORY_SEPARATOR.
            str_replace('\\', DIRECTORY_SEPARATOR, $classname).
            '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }
);
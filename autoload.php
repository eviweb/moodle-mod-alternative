<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * get the current module information
 *
 * @return \stdClass
 */
function alternative_get_module_info()
{
    $module = new stdClass();
    $module->component = '';
    include __DIR__.'/version.php';

    return $module;
}

/**
 * register autoload function
 */
spl_autoload_register(
    function ($class) {
        $module = alternative_get_module_info();
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
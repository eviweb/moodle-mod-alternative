<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * get the current module information
 *
 * @return \stdClass
 */
function alternative_get_module_info()
{
    $plugin = new stdClass();
    $plugin->component = '';
    include __DIR__.'/version.php';

    return $plugin;
}

/**
 * register autoload function
 */
spl_autoload_register(
    function ($class) {
        $plugin = alternative_get_module_info();
        $classname = str_replace($plugin->component.'\\', '', $class);
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
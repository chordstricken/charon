<?php
/**
 * Charon Core Include
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since Feb 18, 2015
 * @copyright 2015 Jason Wright
 */

require_once(__DIR__ . '/config.php');

// check php version
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
    echo 'Charon requires PHP Version 5.4 or above.';
    die();
}

// function dependencies
$dependencies = [
    'mcrypt',
    'json',
    'hash',
];

foreach ($dependencies as $key => $lib) {
    if (extension_loaded($lib)) {
        unset($dependencies[$key]);
    }
}

if (count($dependencies)) {
    echo "Charon requires the following dependencies:\n";
    print_r($dependencies);
    die();
}

/**
 * Auto-load classes with default php autoloader
 */
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/lib');
spl_autoload_extensions('.php');
spl_autoload_register('spl_autoload');


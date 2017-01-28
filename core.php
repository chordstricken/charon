<?php
/**
 * Charon Core Include
 * @author Jason Wright <jason@silvermast.io>
 * @since Feb 18, 2015
 * @copyright 2015 Jason Wright
 */
ini_set('error_log', __DIR__ . '/log/error.log');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT);

require_once(__DIR__ . '/config.php');

// check php version
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 70000) {
    echo 'Charon requires PHP Version 7.0 or above.';
    die();
}

/**
 * Auto-load classes with default php autoloader
 */
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/lib');
spl_autoload_extensions('.php');
spl_autoload_register('spl_autoload');


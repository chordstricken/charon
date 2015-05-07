<?php
/**
 * Charon Configuration
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since Feb 18, 2015
 * @copyright 2015 Jason Wright
 */

// verson of charon
define('CHARON_VERSION', '1.2');

// cache key used for encrypting data on disk
define('CACHE_KEY', 'There Chairon stands, who rules the dreary coast - | A sordid god: down from his hairy chin | A length of beard descends, uncombed, unclean; | His eyes, like hollow furnaces on fire; | A girdle, foul with grease, binds his obscene attire.');

// check php version
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
    echo 'Charon requires PHP Version 5.4 or above.';
    die();
}

// function dependencies
$dependencies = [
    'mcrypt_encrypt',
    'mcrypt_decrypt',
];

foreach ($dependencies as $key => $func) {
    if (function_exists($func)) {
        unset($dependencies[$key]);
    }
}

if (count($dependencies)) {
    echo "Charon requires the following dependencies:\n";
    print_r($dependencies);
    die();
}

// define constants
define('ROOT', __DIR__);
define('HTML', ROOT.'/html');

// set ini configurations
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Override error handler
 * @param int $no
 * @param string $str
 * @param string $file
 * @param int $line
 * @param array $context
 */
function charon_error_handler($no, $str, $file, $line, $context) {
    $context = json_encode($context, JSON_PRETTY_PRINT);
    echo "Error $no: $str in $file:$line\n";
    http_response_code(500);
}
set_error_handler('charon_error_handler');

// include /lib classes
foreach (scandir(ROOT.'/lib') as $class) {
    if ($class[0] === '.') {
        continue;
    }
    require_once(ROOT."/lib/$class");
}

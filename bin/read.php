#!/usr/bin/php
<?php
/**
 * Reads a file
 * @author Jason Wright <jason@invexi.com>
 * @since 2/27/15
 * @package charon
 */

require_once(__DIR__.'/../config.php');

if (!isset($_SERVER['argv'][1])) {
    echo "Usage: read.php filename\n";
    die();
}

$name = $_SERVER['argv'][1];

try {
    $file = File::read($name);

} catch (Exception $e) {
    echo $e->getCode() . ' - ' . $e->getMessage() . "\n";
    die(1);
}

echo json_encode($file, JSON_PRETTY_PRINT);
echo "\n";
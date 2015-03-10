#!/usr/bin/php
<?php
/**
 * Removes a User
 * @author Jason Wright <jason@invexi.com>
 * @since 3/8/2015
 * @package charon
 */

require_once(__DIR__.'/../config.php');

$user = readline('User: ');

try {
    User::remove($user);

} catch (Exception $e) {
    echo 'Error ' . $e->getCode() . ' ' . $e->getMessage() . "\n";
}

echo "Success!\n";
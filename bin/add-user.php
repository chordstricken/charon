#!/usr/bin/php
<?php
/**
 * Creates a User
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/27/15
 * @package charon
 */

require_once(__DIR__.'/../core.php');

$user = readline('User: ');

// use hidden password functionality in read library
$pass1 = trim(`/bin/bash -c "read -s -p 'Pass: ' password && echo \\\$password"`);
echo "\n";

// use hidden password functionality in read library
$pass2 = trim(`/bin/bash -c "read -s -p 'Re-enter Pass: ' password && echo \\\$password"`);
echo "\n";

// check passwords
if ($pass1 != $pass2) {
    echo "Passwords do not match\n";
    die();
}

$user = models\User::findOne(['name' => $user]);
$user->setPasswordHash($pass1);
$user->save();

echo "Success!\n";

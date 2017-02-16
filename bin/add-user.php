#!/usr/bin/env php
<?php
/**
 * Creates a User
 * @author Jason Wright <jason@silvermast.io>
 * @since 2/27/15
 * @package charon
 */

require_once(__DIR__.'/../core.php');

$name        = readline('User Name: ');
$user        = models\User::findOne(['name' => $name]) ?? models\User::new(['name' => $name]);
$user->email = readline('User Email: ');


if ($user->id && strtolower(readline("User '$user->name' already exists. Overwrite? (y/n): "))[0] != 'y') {
    echo "Exiting\n";
    die();
}

// use hidden password functionality in read library
$pass1 = trim(`/bin/bash -c "read -s -p 'Enter Password: ' password && echo \\\$password"`);
echo "\n";

// use hidden password functionality in read library
$pass2 = trim(`/bin/bash -c "read -s -p 'Re-enter Password: ' password && echo \\\$password"`);
echo "\n";

// check passwords
if ($pass1 != $pass2) {
    echo "Passwords do not match\n";
    die();
}

$user->setPasswordHash($pass1);
$user->save();

echo "Success!\n";

#!/usr/bin/env php
<?php
/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/4/17
 * @package charon
 */
require_once(__DIR__ . '/../core.php');

use models\User;

echo "Creating Account Owner\n";

// set Admin user

$name            = readline('User Name: ');
$user            = User::findOne(['name' => $name]) ?? User::new(['name' => $name]);
$user->email     = readline('User Email: ');
$user->permLevel = User::PERMLEVELS['Owner'];


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
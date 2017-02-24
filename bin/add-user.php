<?php
/**
 * Creates a User
 * @author Jason Wright <jason@silvermast.io>
 * @since 2/27/15
 * @package charon
 */

require_once(__DIR__ . '/../core.php');
require_once(__DIR__ . '/functions.php');

$name            = trim(readline('User Name: '));
$user            = models\User::findOne(['name' => $name]) ?? models\User::new(['name' => $name]);
$user->email     = trim(readline('User Email: '));
$user->permLevel = models\User::PERMLEVELS['Member'];

$account_slug = isset($account->slug) ? $account->slug : mb_strtolower(trim(readline('Account Slug: ')));
if (!$account = models\Account::findOne(['slug' => $account_slug]))
    finish('Account not found.');

if ($user->id && strtolower(readline("User '$user->name' already exists. Overwrite? (y/n): "))[0] != 'y')
    finish();


// use hidden password functionality in read library
$pass1 = readline_masked('Enter Password: ');
$pass2 = readline_masked('Re-enter Password: ');

// check passwords
if ($pass1 != $pass2)
    finish('Passwords do not match.');

$user->setPasswordHash($pass1);
$user->save();

echo "Successfully created the user.\n";

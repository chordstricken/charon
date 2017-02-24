<?php
/**
 * Creates an Account
 * @author Jason Wright <jason@silvermast.io>
 * @since 2/24/17
 * @package charon
 */

require_once(__DIR__ . '/../core.php');
require_once(__DIR__ . '/functions.php');

$account = new models\Account([
    'slug' => mb_strtolower(trim(readline('Enter Slug (e.g. mycompany): '))),
    'name' => trim(readline('Enter Full Account Name: ')),
]);

if (models\Account::findOne(['slug' => $account->slug]))
    finish('An account with that slug already exists.');

$account->validate()->save();

echo "Successfully created the Account.\n";

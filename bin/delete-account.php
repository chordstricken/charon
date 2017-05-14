<?php
/**
 * Nukes an Account
 * @author Jason Wright <jason@silvermast.io>
 * @since 5/8/2017
 * @package charon
 */

require_once(__DIR__ . '/../core.php');
require_once(__DIR__ . '/functions.php');

try {

    if (!$account = models\Account::findOne(['slug' => mb_strtolower(trim(readline('Enter Slug (e.g. mycompany): ')))]))
        finish('Unable to find an account with the provided slug.');

    if (mb_strtolower(trim(readline("Permanently delete account named '$account->name'? (y/n):")))[0] !== 'y')
        finish();

    $account->delete();

    echo "Successfully deleted the Account and all associated data.\n";

} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    die(1);
}
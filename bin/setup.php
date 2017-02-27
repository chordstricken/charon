<?php
/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/4/17
 * @package charon
 */
require_once(__DIR__ . '/../core.php');
require_once(__DIR__ . '/functions.php');

require(__DIR__ . '/add-account.php');
require(__DIR__ . '/add-user.php');

$user->permLevel = models\User::PERMLEVELS['Owner'];
$user->save();

echo "Success!\n";
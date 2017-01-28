<?php
/**
 * Charon Root file and router
 * @author Jason Wright <jason@silvermast.io>
 * @since Feb 18, 2015
 * @copyright 2015 Jason Wright
 */

// load the config file
require_once(__DIR__ . '/../core.php');
session_start();

core\Router::route();
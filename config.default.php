<?php
/**
 * Charon Configuration
 * @author Jason Wright <jason@silvermast.io>
 * @since Feb 18, 2015
 * @copyright 2015 Jason Wright
 */

// set ini configurations
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

date_default_timezone_set('America/Phoenix');

// cache key used for encrypting data on disk
const APP_NAME = 'Charon';
const CRYPT_KEY = 'iAEI9x2VtREp8mkGd1xCGyCd3aU1I+hxpuMRjhGqDtY=';
const ROOT = __DIR__;
const HTML = ROOT.'/html';

<?php
/**
 * Rolls up javascript into single file
 */
header('Content-Type: text/javascript');
include(__DIR__ . '/mcrypt.js');
include(__DIR__ . '/rijndael.js');
include(__DIR__ . '/md5.js');
include(__DIR__ . '/sha256.js');
include(__DIR__ . '/jquery.min.js');
include(__DIR__ . '/bootstrap.min.js');
include(__DIR__ . '/angular.min.js');
include(__DIR__ . '/angular-sortable-view.min.js');
include(__DIR__ . '/functions.js');
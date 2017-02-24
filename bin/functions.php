<?php
/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/24/17
 * @package charon
 */

/**
 * @param null $msg
 * @param int $code
 */
function finish($msg = null, $code = 0) {
    echo ($msg ?? "Exiting") . "\n";
    die($code);
}

/**
 * Masked readline. Useful for passwords
 * @param $prompt
 * @return string
 */
function readline_masked($prompt) {
    $result = trim(`/bin/bash -c "read -s -p '$prompt' result && echo \\\$result"`);
    echo "\n";
    return $result;
}
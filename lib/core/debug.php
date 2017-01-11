<?php
namespace core;
/**
 * Debug class responsible for logging items in /log
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/18/15
 * @package charon
 */

class Debug {

    /**
     * @param mixed $message
     */
    public static function info($message) {
        $message = $message instanceof \Exception ? $message->getMessage() : $message;
        $message = is_scalar($message) ? $message : json_encode($message);

        if (!is_dir(ROOT . '/log'))
            @mkdir(ROOT . '/log');

        @error_log(date(DATE_ATOM) . " -- $message\n", 3, ROOT . '/log/debug.log');
    }

}

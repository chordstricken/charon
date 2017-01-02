<?php
namespace Core;

/**
 * Log class responsible for logging items in /log
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/18/15
 * @package charon
 */

class Log {

    private static function _format_message

    /**
     * @param mixed $message
     */
    public static function info($message) {
        $message = $message instanceof Exception ? $message->getMessage() : json_encode($message);
        error_log(date(DATE_FORMAT) . " -- $message\n");
    }

}

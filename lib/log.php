<?php
namespace Core;

use \Exception;
/**
 * Log class responsible for logging items in /log
 * @author Jason Wright <jason@invexi.com>
 * @since 2/18/15
 * @package charon
 */

class Log {

    /**
     * @param mixed $message
     */
    public static function write($message) {
        $message = $message instanceof Exception ? $message->getMessage() : json_encode($message);
        error_log(date(DATE_ATOM) . " -- $message\n");
    }

}

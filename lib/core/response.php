<?php
namespace core;

/**
 * Request class responsible for pulling POST data and sending a response
 * @author Jason Wright <jason@silvermast.io>
 * @since 3/7/15
 * @package charon
 */

class Response {

    /**
     * Sends an http header, prints the content, and dies
     */
    public static function send($data, $code = 200) {
        $data = is_scalar($data) ? $data : json_encode($data);
        http_response_code($code);

        if (!self::isJson() && file_exists(HTML . "/$code.php"))
            include(HTML . "/$code.php");

        else
            echo $data;

        die();
    }

    /**
     * @return bool
     */
    public static function isJson() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ?? strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    }

}


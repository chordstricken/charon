<?php
/**
 * Request class responsible for pulling POST data and sending a response
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 3/7/15
 * @package charon
 */

class Request {

    /**
     * Singleton factory
     */
    public static function init() {
        return new self();
    }

    /**
     * Sends an http header, prints the content, and dies
     */
    public static function send($data, $code = 200) {
        $data = is_scalar($data) ? $data : json_encode($data);
        http_response_code($code);
        echo $data;
        die();
    }

    /**
     * Obtains the request payload. This is often a JSON string that has not been parsed into a global.
     * @return mixed
     */
    public static function get() {
        return json_decode(file_get_contents('php://input'));
    }

    public static function is_json() {
        return strpos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'application/json') !== false;
    }

}


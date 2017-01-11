<?php
namespace api;

use models;
use core;

/**
 * base class for managing API requests
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/2/17
 * @package charon
 */
abstract class Base {

    /** @var array */
    protected $path;

    /** @var mixed */
    protected $data;

    /** @var bool */
    protected $require_auth = true;

    /** @var bool */
    protected $is_json = true;

    /**
     * Base constructor.
     */
    public function __construct($path = null, $data = null) {
        $this->path    = $path;
        $this->data    = $data ?? json_decode(file_get_contents('php://input'));
        $this->is_json = strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

        if ($this->require_auth && !models\User::me()) {

            if (!$this->is_json) {
                http_response_code(401);
                require_once(HTML . '/login.php');
                die();
            }

            core\Response::send('Please log in.', 401);
        }
    }

    /**
     * Sends an HTTP response, output data, and stops execution.
     * @param $response
     * @param $code
     */
    protected function send($data, $code = 200) {
        $data = is_scalar($data) ? $data : json_encode($data);
        http_response_code($code);
        echo $data;
        die();
    }
}
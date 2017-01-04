<?php
namespace api;

use \User;
use \Request;

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

    /**
     * Base constructor.
     */
    public function __construct($path = null, $data = null) {
        $this->path = $path;
        $this->data = $data;

        if ($this->require_auth)
            try {
                User::check_auth();
            } catch (\Exception $e) {
                if (!Request::is_json()) {
                    http_response_code($e->getCode());
                    require_once(HTML . '/login.php');
                    die();
                }

                Request::send('Please log in.', $e->getCode());
            }
    }

}
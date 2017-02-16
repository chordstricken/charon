<?php
namespace api;

use core;

/**
 * Logout request route
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/3/17
 * @package charon
 */
class Logout extends core\APIRoute {

    protected $require_auth = false;
    protected $is_encrypted = false;

    public function get() {
        unset($_SESSION);
        session_destroy();
        $this->send('Suggessfully logged out.');
    }

}
<?php
namespace api;
use \User;

/**
 * Logout request route
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/3/17
 * @package charon
 */
class Logout {

    public function get() {
        unset($_SESSION);
        session_destroy();
        throw new \Exception('Suggessfully logged out.', 401);
    }

}